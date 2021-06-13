<?php

namespace App\Http\Controllers;

use App\InvoiceStock;
use App\Party;
use App\Product;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductPurchaseDetail;
use App\ProductSale;
use App\ProductSaleDetail;
use App\ProductSaleReplacement;
use App\ProductSaleReplacementDetail;
use App\ProductSaleReturn;
use App\ProductSubCategory;
use App\ProductUnit;
use App\Profit;
use App\Stock;
use App\Store;
use App\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSaleReplacementController extends Controller
{

    public function index()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $productSaleReplacements = ProductSaleReplacement::latest()->get();
        }else{
            $productSaleReplacements = ProductSaleReplacement::where('user_id',$auth_user_id)->latest()->get();
        }
        return view('backend.productSaleReplacement.index',compact('productSaleReplacements'));
    }


    public function create()
    {
       // dd('kk');
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $parties = Party::where('type','customer')->get() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $productSales = ProductSale::latest()->get();

        return view('backend.productSaleReplacement.create',compact('parties','stores','productSales'));
    }



    public function getSaleProduct($sale_id){
        //$products = ProductSaleDetail::where('product_sale_id',$sale_id)->get();
        $products = DB::table('product_sale_details')
            ->join('products','product_sale_details.product_id','=','products.id')
            ->where('product_sale_details.product_sale_id',$sale_id)
            ->select('product_sale_details.product_id','product_sale_details.qty','product_sale_details.price','products.name')
            ->get();

        $html = "<table class=\"table table-striped tabel-penjualan\">
                        <thead>
                            <tr>
                                <th width=\"30\">No</th>
                                <th>Product Name</th>
                                <th align=\"right\"> Quantity</th>
                                <th>Replace Quantity</th>
                                <th style=\"display: none\">Price</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>";
                        if(count($products) > 0):
                            foreach($products as $key => $item):
                                $key += 1;
                                $html .= "<tr>";
                                $html .= "<th width=\"30\">1</th>";
                                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_id[]\" id=\"product_id_$key\" value=\"$item->product_id\" size=\"28\" />$item->name</th>";
                                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"qty[]\" id=\"qty_$key\" value=\"$item->qty\" size=\"28\" readonly /></th>";
                                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"replace_qty[]\" id=\"replace_qty_$key\" onkeyup=\"replace_qty($key,this);\" size=\"28\" /></th>";
                                $html .= "<th style=\"display: none\"><input type=\"text\" class=\"form-control\" name=\"price[]\" id=\"price_$key\" value=\"$item->price\" size=\"28\" /></th>";
                                $html .= "<th><textarea type=\"text\" class=\"form-control\" name=\"reason[]\" id=\"reason_$key\"  size=\"28\" ></textarea> </th>";
                                $html .= "</tr>";
                            endforeach;
                            //$html .= "<tr><th align=\"right\" colspan=\"7\"><input type=\"button\" class=\"btn btn-danger\" name=\"remove\" id=\"remove\" size=\"28\" value=\"Clear Item\" onClick=\"deleteAllCart()\" /></th></tr>";
                        endif;
                        $html .= "</tbody>
                    </table>";
        echo json_encode($html);
    }


    public function store(Request $request)
    {
        //dd($request->all());
        $row_count = count($request->replace_qty);

        $productSale = ProductSale::where('id',$request->product_sale_id)->first();
        $purchase_invoice_no = ProductSaleDetail::where('product_sale_id',$productSale->id)->pluck('purchase_invoice_no')->first();
        // product replacement
        $purchase_sale_replacement = new ProductSaleReplacement();
        $purchase_sale_replacement->invoice_no = 'salrep-'.$productSale->invoice_no;
        $purchase_sale_replacement->sale_invoice_no = $productSale->invoice_no;
        $purchase_sale_replacement->product_sale_id = $request->product_sale_id;
        $purchase_sale_replacement->user_id = Auth::user()->id;
        $purchase_sale_replacement->store_id = $productSale->store_id;
        $purchase_sale_replacement->party_id = $productSale->party_id;
        $purchase_sale_replacement->date = date('Y-m-d');
        $purchase_sale_replacement->save();
        $insert_id = $purchase_sale_replacement->id;

        $total_amount = 0;
        for ($i = 0; $i < $row_count; $i++) {
            if ($request->replace_qty[$i] != null) {
                $total_amount += $request->replace_qty[$i]*$request->price[$i];
            }
        }

        if($insert_id){
            for($i=0; $i<$row_count;$i++)
            {
                if($request->replace_qty[$i] != null){
                    // product replacement detail
                    $purchase_sale_replacement_detail = new ProductSaleReplacementDetail();
                    $purchase_sale_replacement_detail->p_s_replacement_id = $insert_id;
                    $purchase_sale_replacement_detail->product_id = $request->product_id[$i];
                    $purchase_sale_replacement_detail->replace_qty = $request->replace_qty[$i];
                    $purchase_sale_replacement_detail->price = $request->price[$i];
                    $purchase_sale_replacement_detail->reason = $request->reason[$i];
                    $purchase_sale_replacement_detail->save();

                    $product_id = $request->product_id[$i];

                    // update purchase details table stock status
                    $product_purchase_details_info = ProductPurchaseDetail::where('invoice_no',$purchase_invoice_no)->where('product_id',$product_id)->first();
                    $purchase_qty = $product_purchase_details_info->qty;
                    $purchase_previous_sale_qty = $product_purchase_details_info->sale_qty;
                    $total_sale_qty = $purchase_previous_sale_qty - $request->replace_qty[$i];
                    $product_purchase_details_info->sale_qty = $total_sale_qty;
                    if($total_sale_qty == $purchase_qty){
                        $product_purchase_details_info->qty_stock_status = 'Not Available';
                    }else{
                        $product_purchase_details_info->qty_stock_status = 'Available';
                    }
                    $product_purchase_details_info->save();


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
                    $stock->store_id = $productSale->store_id;
                    $stock->date = date('Y-m-d');
                    $stock->product_id = $product_id;
                    $stock->stock_type = 'replace';
                    $stock->previous_stock = $previous_stock;
                    $stock->stock_in = 0;
                    $stock->stock_out = $request->replace_qty[$i];
                    $stock->current_stock = $previous_stock - $request->replace_qty[$i];
                    $stock->save();

                    // invoice wise product stock
                    $check_previous_invoice_stock = InvoiceStock::where('store_id',$productSale->store_id)
                        ->where('purchase_invoice_no',$purchase_invoice_no)
                        ->where('product_id',$product_id)
                        ->latest()
                        ->pluck('current_stock')
                        ->first();

                    if(!empty($check_previous_invoice_stock)){
                        $previous_invoice_stock = $check_previous_invoice_stock;
                    }else{
                        $previous_invoice_stock = 0;
                    }
                    // product stock
                    $invoice_stock = new InvoiceStock();
                    $invoice_stock->user_id = Auth::id();
                    $invoice_stock->ref_id = $insert_id;
                    $invoice_stock->purchase_invoice_no = $purchase_invoice_no;
                    $invoice_stock->invoice_no = 'salrep-'.$productSale->invoice_no;
                    $invoice_stock->store_id = $productSale->store_id;
                    $invoice_stock->date = date('Y-m-d');
                    $invoice_stock->product_id = $product_id;
                    $invoice_stock->stock_type = 'replace';
                    $invoice_stock->previous_stock = $previous_invoice_stock;
                    $invoice_stock->stock_in = 0;
                    $invoice_stock->stock_out = $request->replace_qty[$i];
                    $invoice_stock->current_stock = $previous_invoice_stock - $request->replace_qty[$i];
                    $invoice_stock->save();


                    $profit_amount = get_profit_amount($purchase_invoice_no,$product_id);

                    // profit table
                    $profit = new Profit();
                    $profit->ref_id = $insert_id;
                    $profit->purchase_invoice_no = $purchase_invoice_no;
                    $profit->invoice_no ='salrep-'.$productSale->invoice_no;
                    $profit->user_id = Auth::id();
                    $profit->store_id = $productSale->store_id;
                    $profit->type = 'Sale';
                    $profit->product_id = $product_id;
                    $profit->qty = $request->replace_qty[$i];
                    $profit->price = $request->price[$i];
                    $profit->sub_total = $request->replace_qty[$i]*$request->price[$i];
                    $profit->discount_amount = 0;
                    $profit->profit_amount = -($profit_amount*$request->replace_qty[$i]);
                    $profit->date = date('Y-m-d');
                    $profit->save();
                }
            }
        }

        Toastr::success('Product Sale Created Successfully', 'Success');
        return redirect()->route('productSaleReplacement.index');
    }


    public function show($id)
    {
        $productSaleReplacement = ProductSaleReplacement::find($id);
        $productSaleReplacementDetails = ProductSaleReplacementDetail::where('p_s_replacement_id',$id)->get();

        return view('backend.productSaleReplacement.show', compact('productSaleReplacement','productSaleReplacementDetails'));
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
        $parties = Party::where('type','customer')->get() ;
        $products = Product::all();
        $productBrands = ProductBrand::all();
        $productSaleReplacement = ProductSaleReplacement::find($id);
        $productSaleReplacementDetails = ProductSaleReplacementDetail::where('p_s_replacement_id',$id)->get();

        return view('backend.productSaleReplacement.edit',compact('parties','stores','products','productSaleReplacement','productSaleReplacementDetails','productBrands'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());

        $row_count = count($request->replace_qty);

        for($i=0; $i<$row_count;$i++) {
            if ($request->replace_qty[$i] != null) {
                // product replacement detail
                $product_Sale_replacement_detail_id = $request->product_Sale_replacement_detail_id[$i];
                $purchase_sale_replacement_detail = ProductSaleReplacementDetail::find($product_Sale_replacement_detail_id);
                $purchase_sale_replacement_detail->replace_qty = $request->replace_qty[$i];
                $purchase_sale_replacement_detail->reason = $request->reason[$i];
                $purchase_sale_replacement_detail->save();


                $product_id = $request->product_id[$i];

                // product stock
                $stock_row = Stock::where('ref_id',$id)->where('stock_type','replace')->where('product_id',$product_id)->first();

                if($stock_row->stock_out != $request->replace_qty[$i]) {

                    if ($request->replace_qty[$i] > $stock_row->stock_out) {
                        $add_or_minus_stock_out = $request->replace_qty[$i] - $stock_row->stock_out;
                        $update_stock_out = $stock_row->stock_out + $add_or_minus_stock_out;
                        $update_current_stock = $stock_row->current_stock - $add_or_minus_stock_out;
                    } else {
                        $add_or_minus_stock_out = $stock_row->stock_out - $request->replace_qty[$i];
                        $update_stock_out = $stock_row->stock_out - $add_or_minus_stock_out;
                        $update_current_stock = $stock_row->current_stock + $add_or_minus_stock_out;
                    }


                    $stock_row->user_id = Auth::user()->id;
                    $stock_row->stock_out = $update_stock_out;
                    $stock_row->current_stock = $update_current_stock;
                    $stock_row->update();


                    $product_id = $purchase_sale_replacement_detail->product_id;
                    $product_sale_replacement = ProductSaleReplacement::find($request->purchase_sale_replacement_id);
                    $invoice_no = $product_sale_replacement->invoice_no;
                    $sale_invoice_no = $product_sale_replacement->sale_invoice_no;
                    $store_id = $product_sale_replacement->store_id;
                    $request_qty = $request->replace_qty[$i];

                    $purchase_invoice_no = DB::table('product_sale_details')
                        ->join('product_sales','product_sale_details.product_sale_id','product_sales.id')
                        ->where('product_sales.invoice_no',$sale_invoice_no)
                        ->latest('product_sale_details.purchase_invoice_no')
                        ->pluck('product_sale_details.purchase_invoice_no')
                        ->first();
                    // update purchase details table stock status
                    $product_purchase_details_info = ProductPurchaseDetail::where('invoice_no',$purchase_invoice_no)->where('product_id',$product_id)->first();
                    $purchase_qty = $product_purchase_details_info->qty;
                    $purchase_previous_sale_qty = $product_purchase_details_info->sale_qty;
                    $total_sale_qty = $purchase_previous_sale_qty - $request->qty[$i];
                    $product_purchase_details_info->sale_qty = $total_sale_qty;
                    if($total_sale_qty == $purchase_qty){
                        $product_purchase_details_info->qty_stock_status = 'Not Available';
                    }else{
                        $product_purchase_details_info->qty_stock_status = 'Available';
                    }
                    $product_purchase_details_info->save();




                    // invoice stock
                    $invoice_stock_row = current_invoice_stock_row($store_id,'Finish Goods','replace',$product_id,$purchase_invoice_no,$invoice_no);
                    $previous_invoice_stock = $invoice_stock_row->previous_stock;
                    $invoice_stock_out = $invoice_stock_row->stock_out;

                    if($invoice_stock_out != $request_qty){
                        $invoice_stock_row->user_id = Auth::id();
                        $invoice_stock_row->store_id = $store_id;
                        $invoice_stock_row->date = date('Y-m-d');
                        $invoice_stock_row->product_id = $product_id;
                        $invoice_stock_row->previous_stock = $previous_invoice_stock;
                        $invoice_stock_row->stock_in = 0;
                        $invoice_stock_row->stock_out = $request_qty;
                        $new_stock_out = $previous_invoice_stock - $request_qty;
                        $invoice_stock_row->current_stock = $new_stock_out;
                        $invoice_stock_row->update();
                    }





                    $profit_amount = get_profit_amount($purchase_invoice_no,$product_id);

                    // profit table
                    $profit = get_profit_amount_row($store_id,$purchase_invoice_no,$invoice_no,$product_id);
                    $profit->user_id = Auth::id();
                    $profit->store_id = $store_id;
                    $profit->product_id = $product_id;
                    $profit->qty = $request_qty;
                    $profit->price = $request->price[$i];
                    $profit->sub_total = $request_qty*$request->price[$i];
                    $profit->discount_amount = 0;
                    $profit->profit_amount = -($profit_amount*$request_qty);
                    $profit->date = date('Y-m-d');
                    $profit->update();

                }


            }
        }

        Toastr::success('Product Sale Updated Successfully', 'Success');
        return redirect()->route('productSaleReplacement.index');
    }


    public function destroy($id)
    {
        $productSaleReplacement = ProductSaleReplacement::find($id);
        $productSaleReplacement->delete();

        DB::table('product_sale_replacement_details')->where('p_s_replacement_id',$id)->delete();
        DB::table('stocks')->where('ref_id',$id)->where('stock_type','replace')->delete();
        //DB::table('transactions')->where('ref_id',$id)->delete();

        Toastr::success('Product Sale Replacement Deleted Successfully', 'Success');
        return redirect()->route('productSaleReplacement.index');
    }
}
