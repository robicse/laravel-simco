<?php

namespace App\Http\Controllers;

use App\Due;
use App\Party;
use App\Product;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductPurchaseDetail;
use App\ProductSale;
use App\ProductSaleDetail;
use App\ProductSubCategory;
use App\ProductUnit;
use App\Stock;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use NumberFormatter;

class ProductSaleController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:product-sale-list|product-sale-create|product-sale-edit|product-sale-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-sale-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-sale-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-sale-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $start_date = $request->start_date ? $request->start_date : '';
        $end_date = $request->end_date ? $request->end_date : '';
        if($start_date && $end_date) {
            if ($auth_user == "Admin") {
                $productSales = ProductSale::where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('sale_type', 'whole')->latest('id','desc')->get();
            } else {
                $productSales = ProductSale::where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('sale_type', 'whole')->where('user_id', $auth_user_id)->latest('id','desc')->get();
            }
        }else{
            if ($auth_user == "Admin") {
                $productSales = ProductSale::where('sale_type', 'whole')->latest('id','desc')->get();
            } else {
                $productSales = ProductSale::where('sale_type', 'whole')->where('user_id', $auth_user_id)->latest('id','desc')->get();
            }
        }
        return view('backend.productSale.index',compact('productSales','start_date','end_date'));
    }


    function product_store_stock_sync($product_id,$store_id){

        $stock_data = Stock::where('product_id',$product_id)->where('store_id',$store_id)->get();
        $row_count = count($stock_data);
        if($row_count > 0){
            $store_previous_row_current_stock = null;
            $stock_in_flag = 0;
            $stock_out_flag = 0;

            foreach ($stock_data as $key => $data){

                $id = $data->id;
                $previous_stock = $data->previous_stock;
                $stock_in = $data->stock_in;
                $stock_out = $data->stock_out;
                $current_stock = $data->current_stock;



                if($key == 0){
//                    echo 'row_id =>'.$id.'<br/>';
//                    echo 'product_id =>'.$product_id.'<br/>';
//                    echo 'store_id =>'.$store_id.'<br/>';
//
//                    echo 'store_previous_row_current_stock '.$store_previous_row_current_stock.'<br/>';
//                    echo 'this_row_current_stock =>'.$current_stock.'<br/>';
//                    echo '<br/>';

                    $stock = Stock::find($id);
                    $stock->previous_stock = 0;
                    $stock->current_stock = $stock_in;
                    $affectedRow = $stock->update();
                    if($affectedRow){
//                        echo 'this_row_current_stock => updated => '.$stock_in.'<br/>';
//                        echo '<br/>';
                        $current_stock = $stock->current_stock;
                    }

                }else{
//                    echo 'row_id =>'.$id.'<br/>';
//                    echo 'product_id =>'.$product_id.'<br/>';
//                    echo 'store_id =>'.$store_id.'<br/>';
//
//                    echo 'store_previous_row_current_stock '.$store_previous_row_current_stock.'<br/>';
//                    echo 'this_row_current_stock =>'.$current_stock.'<br/>';
//                    echo '<br/>';

                    // update part
                    if($stock_in > 0){
                        if($stock_in_flag == 1){
                            $stock = Stock::find($id);
                            $stock->previous_stock = $store_previous_row_current_stock;
                            $stock->current_stock = $store_previous_row_current_stock + $stock_in;
                            $affectedRow = $stock->update();
                            if($affectedRow){
//                                echo 'this_row_current_stock => updated => '.$stock_in.'<br/>';
//                                echo '<br/>';
                                $current_stock = $stock->current_stock;
                            }
                        }else if($previous_stock != $store_previous_row_current_stock){
                            $stock_in_flag = 1;

                            $stock = Stock::find($id);
                            $stock->previous_stock = $store_previous_row_current_stock;
                            $stock->current_stock = $store_previous_row_current_stock + $stock_in;
                            $affectedRow = $stock->update();
                            if($affectedRow){
//                                echo 'this_row_current_stock => updated => '.$stock_in.'<br/>';
//                                echo '<br/>';
                                $current_stock = $stock->current_stock;
                            }
                        }else{
//                            echo 'this_row_current_stock => nothing => '.$stock_in.'<br/>';
//                            echo '<br/>';
                        }
                    }else if($stock_out > 0){
                        if($stock_out_flag == 1) {
                            $stock = Stock::find($id);
                            $stock->previous_stock = $store_previous_row_current_stock;
                            $stock->current_stock = $store_previous_row_current_stock - $stock_out;
                            $affectedRow = $stock->update();
                            if ($affectedRow) {
//                                echo 'This Row('.$id.') Current Stock => updated => ' . $stock_out . '<br/>';
//                                echo '<br/>';
                                $current_stock = $stock->current_stock;
                            }
                        }else if($previous_stock != $store_previous_row_current_stock) {
                            $stock_out_flag = 1;

                            $stock = Stock::find($id);
                            $stock->previous_stock = $store_previous_row_current_stock;
                            $stock->current_stock = $store_previous_row_current_stock - $stock_out;
                            $affectedRow = $stock->update();
                            if ($affectedRow) {
//                                echo 'This Row('.$id.') Current Stock => updated =>' . $stock_out . '<br/>';
//                                echo '<br/>';
                                $current_stock = $stock->current_stock;
                            }
                        }else{
//                            echo 'this_row_current_stock => nothing => '.$stock_out.'<br/>';
//                            echo '<br/>';
                        }
                    }else{
//                        echo 'this_row_current_stock => nothing<br/>';
//                        echo '<br/>';
                    }
//                    echo '<br/>';
                }
                $store_previous_row_current_stock = $current_stock;
            }
        }else{
//            echo 'no found!'.'<br/>';
        }
    }


    function stock_sync(){
        $stock_data = Stock::whereIn('id', function($query) {
            $query->from('stocks')->groupBy('store_id')->groupBy('product_id')->selectRaw('MIN(id)');
        })->get();

        $row_count = count($stock_data);
        if($row_count > 0){
            foreach ($stock_data as $key => $data){
                $product_id = $data->product_id;
                $store_id = $data->store_id;
                $this->product_store_stock_sync($product_id,$store_id);
            }
            //Toastr::success('Stock Synchronize Successfully Updated!', 'Success');
        }
        return redirect()->back();
    }


    public function create()
    {

        // stock sync
        $this->stock_sync();


        $auth = Auth::user();
        $auth_user = Auth::user()->roles[0]->name;
        $parties = Party::where('type','customer')->get() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('id',$auth->store_id)->get();
        }
        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        $productUnits = ProductUnit::all();
        $products = Product::where('product_type','Finish Goods')->get();
        return view('backend.productSale.create',compact('parties','stores','products','productCategories','productSubCategories','productBrands','productUnits'));
    }


    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request, [
            'party_id'=> 'required',
            'store_id'=> 'required',

        ]);

        $row_count = count($request->product_id);
        $total_amount = 0;
        for($i=0; $i<$row_count;$i++)
        {
            $total_amount += $request->sub_total[$i];
        }
        $discount_type = $request->discount_type;
        if($discount_type == 'flat'){
            $total_amount -= $request->discount_amount;
        }else{
            $total_amount = ($total_amount*$request->discount_amount)/100;
        }

        $get_invoice_no = ProductSale::latest()->pluck('invoice_no')->first();
        //dd($get_invoice_no);
        if(!empty($get_invoice_no)){
            $invoice_no = $get_invoice_no+1;
        }else{
            $invoice_no = 1000;
        }
        //dd($invoice_no);

        // product purchase
        $productSale = new ProductSale();
        $productSale->invoice_no = $invoice_no;
        $productSale->user_id = Auth::id();
        $productSale->party_id = $request->party_id;
        $productSale->store_id = $request->store_id;
        $productSale->date = $request->date;
        //$productSale->payment_type = $request->payment_type;
        //$productSale->check_number = $request->check_number ? $request->check_number : '';
        $productSale->delivery_service = $request->delivery_service;
        $productSale->delivery_service_charge = $request->delivery_service_charge;
        $productSale->discount_type = $request->discount_type;
        $productSale->discount_amount = $request->discount_amount;
        $productSale->total_amount = $total_amount;
        $productSale->paid_amount = $request->paid_amount;
        $productSale->due_amount = $request->due_amount;
        $productSale->sale_type = 'whole';
        $productSale->save();
        $insert_id = $productSale->id;
        if($insert_id)
        {
            for($i=0; $i<$row_count;$i++)
            {
                // product purchase detail
                $purchase_sale_detail = new ProductSaleDetail();
                $purchase_sale_detail->product_sale_id = $insert_id;
                $purchase_sale_detail->return_type = $request->return_type[$i];
                $purchase_sale_detail->product_category_id = $request->product_category_id[$i];
                $purchase_sale_detail->product_sub_category_id = $request->product_sub_category_id[$i] ? $request->product_sub_category_id[$i] : NULL;
                $purchase_sale_detail->product_brand_id = $request->product_brand_id[$i];
                $purchase_sale_detail->product_unit_id = $request->product_unit_id[$i];
                $purchase_sale_detail->product_id = $request->product_id[$i];
                $purchase_sale_detail->qty = $request->qty[$i];
                $purchase_sale_detail->price = $request->price[$i];
                $purchase_sale_detail->sub_total = $request->qty[$i]*$request->price[$i];
                $purchase_sale_detail->save();

                $product_id = $request->product_id[$i];
                $check_previous_stock = Stock::where('product_id',$product_id)->latest()->pluck('current_stock')->first();
                if(!empty($check_previous_stock)){
                    $previous_stock = $check_previous_stock;
                }else{
                    $previous_stock = 0;
                }
                // product stock
                $stock = new Stock();
                $stock->user_id = Auth::id();
                $stock->ref_id = $insert_id;
                $stock->store_id = $request->store_id;
                $stock->date = $request->date;
                $stock->product_id = $request->product_id[$i];
                $stock->stock_type = 'sale';
                $stock->previous_stock = $previous_stock;
                $stock->stock_in = 0;
                $stock->stock_out = $request->qty[$i];
                $stock->current_stock = $previous_stock - $request->qty[$i];
                $stock->save();
            }

            // due
            $due = new Due();
            $due->invoice_no = $invoice_no;
            $due->ref_id = $insert_id;
            $due->user_id = Auth::id();
            $due->store_id = $request->store_id;
            $due->party_id = $request->party_id;
            //$due->payment_type = $request->payment_type;
            //$due->check_number = $request->check_number ? $request->check_number : '';
            $due->total_amount = $total_amount;
            $due->paid_amount = $request->paid_amount;
            $due->due_amount = $request->due_amount;
            $due->save();

            // transaction
            $transaction = new Transaction();
            $transaction->invoice_no = $invoice_no;
            $transaction->user_id = Auth::id();
            $transaction->store_id = $request->store_id;
            $transaction->party_id = $request->party_id;
            $transaction->date = $request->date;
            $transaction->ref_id = $insert_id;
            $transaction->transaction_type = 'sale';
            $transaction->payment_type = $request->payment_type;
            $transaction->check_number = $request->check_number ? $request->check_number : '';
            //$transaction->amount = $total_amount;
            $transaction->amount = $request->paid_amount;
            $transaction->save();
        }

        Toastr::success('Product Sale Created Successfully', 'Success');
        if($request->print_now == 1){
            //return redirect()->route('productSales-invoice',$insert_id);
            return redirect()->route('productSales-invoice-print',$insert_id);
        }else{
            return redirect()->route('productSales.index');
        }

    }


    public function show($id)
    {
        $productSale = ProductSale::find($id);
        $productSaleDetails = ProductSaleDetail::where('product_sale_id',$id)->get();
        $transaction = Transaction::where('ref_id',$id)->first();

        return view('backend.productSale.show', compact('productSale','productSaleDetails','transaction'));
    }


    public function edit($id)
    {
        $auth = Auth::user();
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('id',$auth->store_id)->get();
        }
        $parties = Party::where('type','customer')->get() ;
        $products = Product::where('product_type','Finish Goods')->get();
        $productSale = ProductSale::find($id);
        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        $productUnits = ProductUnit::all();
        $productSaleDetails = ProductSaleDetail::where('product_sale_id',$id)->get();
        $transaction = Transaction::where('ref_id',$id)->first();
        $stock_id = Stock::where('ref_id',$id)->where('stock_type','purchase')->pluck('id')->first();
        return view('backend.productSale.edit',compact('parties','stores','products','productSale','productSaleDetails','productCategories','productSubCategories','productBrands','productUnits','transaction','stock_id'));
    }


    public function update(Request $request, $id)
    {
        //dd($request->all());
        $this->validate($request, [
            'party_id'=> 'required',
            'store_id'=> 'required',

        ]);

        $stock_id = $request->stock_id;
        $row_count = count($request->product_id);
        $total_amount = 0;
        for($i=0; $i<$row_count;$i++)
        {
            $total_amount += $request->sub_total[$i];
        }

        $discount_type = $request->discount_type;
        if($discount_type == 'flat'){
            $total_amount -= $request->discount_amount;
        }else{
            $total_amount = ($total_amount*$request->discount_amount)/100;
        }

        // product sale
        $productSale = ProductSale::find($id);
        $productSale->user_id = Auth::id();
        $productSale->party_id = $request->party_id;
        $productSale->store_id = $request->store_id;
        $productSale->delivery_service = $request->delivery_service;
        $productSale->delivery_service_charge = $request->delivery_service_charge;
        $productSale->discount_type = $request->discount_type;
        $productSale->discount_amount = $request->discount_amount;
        $productSale->total_amount = $total_amount;
        $productSale->paid_amount = $request->paid_amount;
        $productSale->due_amount = $request->due_amount;
        $productSale->update();

        for($i=0; $i<$row_count;$i++)
        {
            // product purchase detail
            $product_sale_detail_id = $request->product_Sale_detail_id[$i];
            //dd($product_sale_detail_id);
            $purchase_sale_detail = ProductsaleDetail::findOrFail($product_sale_detail_id);
            //dd($purchase_sale_detail);
            $purchase_sale_detail->return_type = $request->return_type[$i];
            $purchase_sale_detail->product_category_id = $request->product_category_id[$i];
            $purchase_sale_detail->product_sub_category_id = $request->product_sub_category_id[$i] ? $request->product_sub_category_id[$i] : NULL;
            $purchase_sale_detail->product_brand_id = $request->product_brand_id[$i];
            $purchase_sale_detail->product_id = $request->product_id[$i];
            $purchase_sale_detail->qty = $request->qty[$i];
            $purchase_sale_detail->price = $request->price[$i];
            $purchase_sale_detail->sub_total = $request->qty[$i]*$request->price[$i];
            $purchase_sale_detail->update();


            $product_id = $request->product_id[$i];
            $check_previous_stock = Stock::where('product_id',$product_id)->where('id','!=',$stock_id)->latest()->pluck('current_stock')->first();
            if(!empty($check_previous_stock)){
                $previous_stock = $check_previous_stock;
            }else{
                $previous_stock = 0;
            }
            // product stock
            $stock = Stock::where('ref_id',$id)->where('stock_type','sale')->first();
            $stock->user_id = Auth::id();
            $stock->store_id = $request->store_id;
            $stock->product_id = $request->product_id[$i];
            $stock->previous_stock = $previous_stock;
            $stock->stock_in = 0;
            $stock->stock_out = $request->qty[$i];
            $stock->current_stock = $previous_stock - $request->qty[$i];
            $stock->update();
        }

        // due
        $due = Due::where('ref_id',$id)->first();;
        $due->user_id = Auth::id();
        $due->store_id = $request->store_id;
        $due->party_id = $request->party_id;
        $due->total_amount = $total_amount;
        $due->paid_amount = $request->paid_amount;
        $due->due_amount = $request->due_amount;
        $due->update();

        // transaction
        $transaction = Transaction::where('ref_id',$id)->where('transaction_type','sale')->first();
        $transaction->user_id = Auth::id();
        $transaction->store_id = $request->store_id;
        $transaction->party_id = $request->party_id;
        $transaction->payment_type = $request->payment_type;
        $transaction->check_number = $request->check_number ? $request->check_number : '';
        $transaction->amount = $request->paid_amount;
        $transaction->update();

        Toastr::success('Product Sale Updated Successfully', 'Success');
        return redirect()->route('productSales.index');
    }


    public function destroy($id)
    {
        $productSale = ProductSale::find($id);
        $productSale->delete();

        DB::table('product_sale_details')->where('product_sale_id',$id)->delete();
        DB::table('stocks')->where('ref_id',$id)->where('stock_type','sale')->delete();
        DB::table('transactions')->where('ref_id',$id)->where('transaction_type','sale')->delete();

        Toastr::success('Product Sale Deleted Successfully', 'Success');
        return redirect()->route('productSales.index');
    }

    public function productSaleRelationData(Request $request){
        $store_id = $request->store_id;
        $product_id = $request->current_product_id;
        $current_stock = Stock::where('store_id',$store_id)->where('product_id',$product_id)->latest()->pluck('current_stock')->first();
        $mrp_price = ProductPurchaseDetail::join('product_purchases', 'product_purchase_details.product_purchase_id', '=', 'product_purchases.id')
            ->where('store_id',$store_id)->where('product_id',$product_id)
            ->latest('product_purchase_details.id')
            ->pluck('product_purchase_details.mrp_price')
            ->first();

        $product_category_id = Product::where('id',$product_id)->pluck('product_category_id')->first();
        $product_sub_category_id = Product::where('id',$product_id)->pluck('product_sub_category_id')->first();
        $product_brand_id = Product::where('id',$product_id)->pluck('product_brand_id')->first();
        $product_unit_id = Product::where('id',$product_id)->pluck('product_unit_id')->first();
        $options = [
            'mrp_price' => $mrp_price,
            'current_stock' => $current_stock,
            'categoryOptions' => '',
            'subCategoryOptions' => '',
            'brandOptions' => '',
            'unitOptions' => '',
        ];

        if($product_category_id){
            $categories = ProductCategory::where('id',$product_category_id)->get();
            if(count($categories) > 0){
                $options['categoryOptions'] = "<select class='form-control' name='product_category_id[]' readonly>";
                foreach($categories as $category){
                    $options['categoryOptions'] .= "<option value='$category->id'>$category->name</option>";
                }
                $options['categoryOptions'] .= "</select>";
            }
        }else{
            $options['categoryOptions'] = "<select class='form-control' name='product_sub_category_id[]' readonly>";
            $options['categoryOptions'] .= "<option value=''>No Data Found!</option>";
            $options['categoryOptions'] .= "</select>";
        }
        if(!empty($product_sub_category_id)){
            $subCategories = ProductSubCategory::where('id',$product_sub_category_id)->get();
            if(count($subCategories) > 0){
                $options['subCategoryOptions'] = "<select class='form-control' name='product_sub_category_id[]' readonly>";
                foreach($subCategories as $subCategory){
                    $options['subCategoryOptions'] .= "<option value='$subCategory->id'>$subCategory->name</option>";
                }
                $options['subCategoryOptions'] .= "</select>";
            }
        }else{
            $options['subCategoryOptions'] = "<select class='form-control' name='product_sub_category_id[]' readonly>";
            $options['subCategoryOptions'] .= "<option value=''>No Data Found!</option>";
            $options['subCategoryOptions'] .= "</select>";
        }
        if($product_brand_id){
            $brands = ProductBrand::where('id',$product_brand_id)->get();
            if(count($brands) > 0){
                $options['brandOptions'] = "<select class='form-control' name='product_brand_id[]'readonly>";
                foreach($brands as $brand){
                    $options['brandOptions'] .= "<option value='$brand->id'>$brand->name</option>";
                }
                $options['brandOptions'] .= "</select>";
            }
        }else{
            $options['brandOptions'] = "<select class='form-control' name='product_brand_id[]' readonly>";
            $options['brandOptions'] .= "<option value=''>No Data Found!</option>";
            $options['brandOptions'] .= "</select>";
        }

        if($product_unit_id){
            $units = ProductUnit::where('id',$product_unit_id)->get();
            if(count($units) > 0){
                $options['unitOptions'] = "<select class='form-control' name='product_unit_id[]' readonly>";
                foreach($units as $unit){
                    $options['unitOptions'] .= "<option value='$unit->id'>$unit->name</option>";
                }
                $options['unitOptions'] .= "</select>";
            }
        }else{
            $options['unitOptions'] = "<select class='form-control' name='product_unit_id[]' readonly>";
            $options['unitOptions'] .= "<option value=''>No Data Found!</option>";
            $options['unitOptions'] .= "</select>";
        }

        return response()->json(['success'=>true,'data'=>$options]);
    }

    public function invoice($id)
    {
        $productSale = ProductSale::find($id);
        $productSaleDetails = ProductSaleDetail::where('product_sale_id',$id)->get();
        //$transactions = Transaction::where('ref_id',$id)->where('transaction_type','sale')->get();
        $transactions = Transaction::where('ref_id',$id)->where('invoice_no',$productSale->invoice_no)->get();
        $store_id = $productSale->store_id;
        $party_id = $productSale->party_id;
        $store = Store::find($store_id);
        $party = Party::find($party_id);
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return view('backend.productSale.invoice', compact('productSale','productSaleDetails','transactions','store','party','digit'));
    }
    public function invoicePrint($id)
    {
        $productSale = ProductSale::find($id);
        $productSaleDetails = ProductSaleDetail::where('product_sale_id',$id)->get();
        //$transactions = Transaction::where('ref_id',$id)->where('transaction_type','sale')->get();
        $transactions = Transaction::where('ref_id',$id)->where('invoice_no',$productSale->invoice_no)->get();
        $store_id = $productSale->store_id;
        $party_id = $productSale->party_id;
        $store = Store::find($store_id);
        $party = Party::find($party_id);
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return view('backend.productSale.invoice-print', compact('productSale','productSaleDetails','transactions','store','party','digit'));

    }

    public function invoiceEdit($id)
    {
        $productSale = ProductSale::find($id);
        $productSaleDetails = ProductSaleDetail::where('product_sale_id',$productSale->id)->get();
        $transactions = Transaction::where('ref_id',$id)->where('transaction_type','sale')->get();
        $store_id = $productSale->store_id;
        $party_id = $productSale->party_id;
        $store = Store::find($store_id);
        $party = Party::find($party_id);
        //dd($productSaleDetails);

        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        $products = Product::where('product_type','Finish Goods')->get();
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return view('backend.productSale.invoice-edit', compact('productSale','productSaleDetails','transactions','store','party','productCategories','productSubCategories','productBrands','products'));
    }

    public function updateInvoice(Request $request, $id){
        //dd($id);
        //dd($request->all());

        $row_count = count($request->product_id);
        $total_amount = $request->current_total_amount;
//        $total_amount = 0;
//        for($i=0; $i<$row_count;$i++)
//        {
//            $total_amount += $request->sub_total[$i];
//        }
//        $discount_type = $request->discount_type;
//        if($discount_type == 'flat'){
//            $total_amount -= $request->discount_amount;
//        }else{
//            $total_amount = ($total_amount*$request->discount_amount)/100;
//        }

        for($i=0; $i<$row_count;$i++)
        {
            // product sale detail insert
            $purchase_sale_detail = new ProductSaleDetail();
            $purchase_sale_detail->product_sale_id = $id;
            $purchase_sale_detail->return_type = $request->return_type[$i];
            $purchase_sale_detail->product_category_id = $request->product_category_id[$i];
            $purchase_sale_detail->product_sub_category_id = $request->product_sub_category_id[$i] ? $request->product_sub_category_id[$i] : NULL;
            $purchase_sale_detail->product_brand_id = $request->product_brand_id[$i];
            $purchase_sale_detail->product_id = $request->product_id[$i];
            $purchase_sale_detail->qty = $request->qty[$i];
            $purchase_sale_detail->price = $request->price[$i];
            $purchase_sale_detail->sub_total = $request->qty[$i]*$request->price[$i];
            $purchase_sale_detail->save();

            $product_id = $request->product_id[$i];
            $check_previous_stock = Stock::where('product_id',$product_id)->latest()->pluck('current_stock')->first();
            if(!empty($check_previous_stock)){
                $previous_stock = $check_previous_stock;
            }else{
                $previous_stock = 0;
            }
            // product stock insert
            $stock = new Stock();
            $stock->user_id = Auth::id();
            $stock->ref_id = $id;
            $stock->store_id = $request->store_id;
            $stock->product_id = $request->product_id[$i];
            $stock->stock_type = 'sale';
            $stock->previous_stock = $previous_stock;
            $stock->stock_in = 0;
            $stock->stock_out = $request->qty[$i];
            $stock->current_stock = $previous_stock - $request->qty[$i];
            $stock->date = date('Y-m-d');
            $stock->save();
        }

        // product sale update
        $productSale = ProductSale::find($id);
        $productSale->user_id = Auth::id();
        //$productSale->party_id = $request->party_id;
        $productSale->store_id = $request->store_id;
        //$productSale->payment_type = $request->payment_type;
        //$productSale->delivery_service = $request->delivery_service;
        //$productSale->delivery_service_charge = $request->delivery_service_charge;
        $productSale->discount_type = $request->discount_type;
        $productSale->discount_amount = $request->discount_amount;
        $productSale->total_amount = $total_amount;
        $productSale->paid_amount = $request->paid_amount;
        $productSale->due_amount = $request->due_amount;
        $productSale->update();



        // due update
        $due = Due::where('ref_id',$id)->first();;
        $due->user_id = Auth::id();
        $due->store_id = $request->store_id;
        //$due->party_id = $request->party_id;
        //$due->payment_type = $request->payment_type;
        $due->total_amount = $total_amount;
        $due->paid_amount = $request->paid_amount;
        $due->due_amount = $request->due_amount;
        $due->update();

        // transaction update
        $transaction = Transaction::where('ref_id',$id)->where('transaction_type','sale')->first();
        $transaction->user_id = Auth::id();
        $transaction->store_id = $request->store_id;
        //$transaction->party_id = $request->party_id;
        //$transaction->payment_type = $request->payment_type;
        $transaction->amount = $total_amount;
        $transaction->update();


        Toastr::success('Invoice Updated Successfully', 'Success');
        return redirect()->route('productSales.index');
    }

    public function newParty(Request $request){
        //dd($request->all());
        $this->validate($request, [
            'type'=> 'required',
            'name' => 'required',
            'phone'=> 'required',
            'email'=> '',
            'address'=> '',
        ]);
        $parties = new Party();
        $parties->type = $request->type;
        $parties->name = $request->name;
        $parties->slug = Str::slug($request->name);
        $parties->phone = $request->phone;
        $parties->email = $request->email;
        $parties->address = $request->address;
        $parties->status = 1;
        $parties->save();
        $insert_id = $parties->id;

        if ($insert_id){
            $sdata['id'] = $insert_id;
            $sdata['name'] = $parties->name;
            echo json_encode($sdata);

        }
        else {
            $data['exception'] = 'Some thing mistake !';
            echo json_encode($data);

        }
    }

    public function payDue(Request $request){
        //dd($request->all());
        $product_sale_id = $request->product_sale_id;
        $product_sale = ProductSale::find($product_sale_id);

        $total_amount=$product_sale->total_amount;
        $paid_amount=$product_sale->paid_amount;

        $update_product_sale = ProductSale::find($product_sale_id);
        $update_product_sale->paid_amount=$paid_amount+$request->new_paid;
        $update_product_sale->due_amount=$total_amount-($paid_amount+$request->new_paid);
        $affectedRow = $update_product_sale->update();

        $due = new Due();
        $due->invoice_no=$product_sale->invoice_no;
        $due->ref_id=$request->product_sale_id;
        $due->user_id=$product_sale->user_id;
        $due->store_id=$product_sale->store_id;
        $due->party_id=$product_sale->party_id;
        //$due->payment_type=$product_sale->payment_type;
        $due->total_amount=$product_sale->total_amount;
        $due->paid_amount=$request->new_paid;
        $due->due_amount=$total_amount-($paid_amount+$request->new_paid);
        $due->save();

        // transaction
        $transaction = new Transaction();
        $transaction->invoice_no = $product_sale->invoice_no;
        $transaction->user_id = Auth::id();
        $transaction->store_id = $product_sale->store_id;
        $transaction->party_id = $product_sale->party_id;
        $transaction->ref_id = $product_sale->id;
        $transaction->transaction_type = 'due';
        $transaction->payment_type = $request->payment_type;
        $transaction->check_number = $request->check_number ? $request->check_number : '';
        $transaction->amount = $request->new_paid;
        $transaction->date = date('Y-m-d');
        $transaction->save();

        if($affectedRow){
            Toastr::success('Due Pay Successfully', 'Success');
        }
        return redirect()->back();

    }

    public function customerDue()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $productSales = ProductSale::where('due_amount','>',0)->latest()->get();
        }else{
            $productSales = ProductSale::where('user_id',$auth_user_id)->where('due_amount','>',0)->get();
        }
        return view('backend.productSale.customer_due',compact('productSales'));
    }
}
