<?php

namespace App\Http\Controllers;

use App\Due;
use App\Party;
use App\Product;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductPurchaseDetail;
use App\ProductProduction;
use App\ProductProductionDetail;
use App\ProductSubCategory;
use App\Stock;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductProductionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-production-list|product-production-create|product-production-edit|product-production-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-production-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-production-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-production-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $productProductions = ProductProduction::latest()->get();
        }else{
            $productProductions = ProductProduction::where('user_id',$auth_user_id)->get();
        }
        return view('backend.productProduction.index',compact('productProductions'));
    }


    public function create()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        $products = Product::where('product_type','Raw Materials')->get();
        return view('backend.productProduction.create',compact('stores','products','productCategories','productSubCategories','productBrands'));
    }


    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request, [
            'store_id'=> 'required',
        ]);

        $row_count = count($request->product_id);
        $total_amount = 0;
        for($i=0; $i<$row_count;$i++)
        {
            $total_amount += $request->sub_total[$i];
        }

        // product Production
        $productProduction = new ProductProduction();
        $productProduction->user_id = Auth::id();
        $productProduction->store_id = $request->store_id;
        $productProduction->total_amount = $total_amount;
        $productProduction->paid_amount = 0;
        $productProduction->due_amount = $total_amount;
        $productProduction->date = $request->date;
        $productProduction->save();
        $insert_id = $productProduction->id;
        if($insert_id)
        {
            for($i=0; $i<$row_count;$i++)
            {
                // product production detail
                $purchase_production_detail = new ProductProductionDetail();
                $purchase_production_detail->product_production_id = $insert_id;
                $purchase_production_detail->product_category_id = $request->product_category_id[$i];
                $purchase_production_detail->product_sub_category_id = $request->product_sub_category_id[$i] ? $request->product_sub_category_id[$i] : NULL;
                $purchase_production_detail->product_brand_id = $request->product_brand_id[$i];
                $purchase_production_detail->product_id = $request->product_id[$i];
                $purchase_production_detail->qty = $request->qty[$i];
                $purchase_production_detail->price = $request->price[$i];
                $purchase_production_detail->sub_total = $request->qty[$i]*$request->price[$i];
                $purchase_production_detail->save();

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
                $stock->stock_product_type = 'Raw Materials';
                $stock->stock_type = 'production';
                $stock->previous_stock = $previous_stock;
                $stock->stock_in = 0;
                $stock->stock_out = $request->qty[$i];
                $stock->current_stock = $previous_stock - $request->qty[$i];
                $stock->save();
            }

            // transaction
            $transaction = new Transaction();
            $transaction->invoice_no = NULL;
            $transaction->user_id = Auth::id();
            $transaction->store_id = $request->store_id;
            $transaction->party_id = NULL;
            $transaction->date = $request->date;
            $transaction->ref_id = $insert_id;
            $transaction->transaction_type = 'production';
            $transaction->payment_type = $request->payment_type;
            $transaction->check_number = $request->check_number ? $request->check_number : '';
            $transaction->amount = $total_amount;
            $transaction->save();
        }

        Toastr::success('Product Production Created Successfully', 'Success');
        return redirect()->route('productProductions.index');

    }


    public function show($id)
    {
        $productProduction = ProductProduction::find($id);
        $productProductionDetails = ProductProductionDetail::where('product_production_id',$id)->get();
        $transaction = Transaction::where('ref_id',$id)->first();

        return view('backend.productProduction.show', compact('productProduction','productProductionDetails','transaction'));
    }


    public function edit($id)
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }

        $products = Product::where('product_type','Raw Materials')->get();
        $productProduction = ProductProduction::find($id);
        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        $productProductionDetails = ProductProductionDetail::where('product_production_id',$id)->get();
        $transaction = Transaction::where('ref_id',$id)->first();
        return view('backend.productProduction.edit',compact('stores','products','productProduction','productProductionDetails','productCategories','productSubCategories','productBrands','transaction'));
    }


    public function update(Request $request, $id)
    {
        //dd($request->all());
        $this->validate($request, [
            'store_id'=> 'required',
        ]);

        $row_count = count($request->product_id);
        $total_amount = 0;
        for($i=0; $i<$row_count;$i++)
        {
            $total_amount += $request->sub_total[$i];
        }

        // product purchase
        $productProduction = ProductProduction::find($id);
        $productProduction->user_id = Auth::id();
        $productProduction->store_id = $request->store_id;
        $productProduction->total_amount = $total_amount;
        $productProduction->date = $request->date;
        $productProduction->update();

        for($i=0; $i<$row_count;$i++)
        {
            // product purchase detail
            $product_production_detail_id = $request->product_production_detail_id[$i];
            $purchase_production_detail = ProductProductionDetail::findOrFail($product_production_detail_id);
            $purchase_production_detail->product_category_id = $request->product_category_id[$i];
            $purchase_production_detail->product_sub_category_id = $request->product_sub_category_id[$i] ? $request->product_sub_category_id[$i] : NULL;
            $purchase_production_detail->product_brand_id = $request->product_brand_id[$i];
            $purchase_production_detail->product_id = $request->product_id[$i];
            $purchase_production_detail->qty = $request->qty[$i];
            $purchase_production_detail->price = $request->price[$i];
            $purchase_production_detail->sub_total = $request->qty[$i]*$request->price[$i];
            $purchase_production_detail->update();


            $product_id = $request->product_id[$i];
            $check_previous_stock = Stock::where('product_id',$product_id)->latest()->pluck('current_stock')->first();
            if(!empty($check_previous_stock)){
                $previous_stock = $check_previous_stock;
            }else{
                $previous_stock = 0;
            }
            // product stock
            $stock = Stock::where('ref_id',$id)->where('stock_type','production')->first();
            $stock->user_id = Auth::id();
            $stock->date = $request->date;
            $stock->product_id = $request->product_id[$i];
            $stock->stock_type = 'production';
            $stock->previous_stock = $previous_stock;
            $stock->stock_in = 0;
            $stock->stock_out = $request->qty[$i];
            $stock->current_stock = $previous_stock - $request->qty[$i];
            $stock->update();
        }

        // transaction
        $transaction = Transaction::where('ref_id',$id)->where('transaction_type','production')->first();
        $transaction->user_id = Auth::id();
        $transaction->store_id = $request->store_id;
        $transaction->date = $request->date;
        $transaction->transaction_type = 'production';
        $transaction->amount = $total_amount;
        $transaction->update();

        Toastr::success('Product Sale Updated Successfully', 'Success');
        return redirect()->route('productProductions.index');
    }


    public function destroy($id)
    {
        $productProduction = ProductProduction::find($id);
        $productProduction->delete();

        DB::table('product_production_details')->where('product_production_id',$id)->delete();
        DB::table('stocks')->where('ref_id',$id)->delete();
        DB::table('transactions')->where('ref_id',$id)->delete();

        Toastr::success('Product Sale Deleted Successfully', 'Success');
        return redirect()->route('productProductions.index');
    }

    public function productProductionRelationData(Request $request){
        $store_id = $request->store_id;
        $product_id = $request->current_product_id;
        $current_stock = Stock::where('store_id',$store_id)->where('product_id',$product_id)->latest()->pluck('current_stock')->first();
        $mrp_price = ProductPurchaseDetail::join('product_purchases', 'product_purchase_details.product_purchase_id', '=', 'product_purchases.id')
            ->where('store_id',$store_id)->where('product_id',$product_id)
            ->latest('product_purchase_details.id')
            ->pluck('product_purchase_details.price')
            ->first();

        $product_category_id = Product::where('id',$product_id)->pluck('product_category_id')->first();
        $product_sub_category_id = Product::where('id',$product_id)->pluck('product_sub_category_id')->first();
        $product_brand_id = Product::where('id',$product_id)->pluck('product_brand_id')->first();
        $options = [
            'mrp_price' => $mrp_price,
            'current_stock' => $current_stock,
            'categoryOptions' => '',
            'subCategoryOptions' => '',
            'brandOptions' => '',
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
            $options['brandOptions'] = "<select class='form-control' name='product_sub_category_id[]' readonly>";
            $options['brandOptions'] .= "<option value=''>No Data Found!</option>";
            $options['brandOptions'] .= "</select>";
        }

        return response()->json(['success'=>true,'data'=>$options]);
    }

}
