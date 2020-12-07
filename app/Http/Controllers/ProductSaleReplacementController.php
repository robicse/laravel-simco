<?php

namespace App\Http\Controllers;

use App\Party;
use App\Product;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductSale;
use App\ProductSaleDetail;
use App\ProductSaleReplacement;
use App\ProductSubCategory;
use App\ProductUnit;
use App\Stock;
use App\Store;
use Brian2694\Toastr\Facades\Toastr;
use Gloudemans\Shoppingcart\Facades\Cart;
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
            ->select('product_sale_details.product_id','product_sale_details.qty','products.name')
            ->get();

        $html = "<table class=\"table table-striped tabel-penjualan\">
                        <thead>
                            <tr>
                                <th width=\"30\">No</th>
                                <th>Product Name</th>
                                <th align=\"right\">Sale Quantity</th>
                                <th>Replace Quantity</th>
                            </tr>
                        </thead>
                        <tbody>";
                        if(count($products) > 0):
                            foreach($products as $item):
                                $html .= "<tr>";
                                $html .= "<th width=\"30\">1</th>";
                                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_id[]\" value=\"$item->product_id\" size=\"28\" />$item->name</th>";
                                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"qty[]\" value=\"$item->qty\" size=\"28\" readonly /></th>";
                                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"replace_qty[]\" size=\"28\" /></th>";
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
        for($i=0; $i<$row_count;$i++)
        {

            if($request->replace_qty[$i] != null){
                $productSale = ProductSale::where('id',$request->product_sale_id)->first();

                // product replacement
                $purchase_sale_replacement = new ProductSaleReplacement();
                $purchase_sale_replacement->invoice_no = 'replace-'.$productSale->invoice_no;
                $purchase_sale_replacement->sale_invoice_no = $productSale->invoice_no;
                $purchase_sale_replacement->product_sale_id = $request->product_sale_id;
                $purchase_sale_replacement->user_id = Auth::user()->id;
                $purchase_sale_replacement->store_id = $productSale->store_id;
                $purchase_sale_replacement->party_id = $productSale->party_id;
                $purchase_sale_replacement->product_id = $request->product_id[$i];
                $purchase_sale_replacement->replace_qty = $request->replace_qty[$i];
                $purchase_sale_replacement->price = $request->price[$i];
                $purchase_sale_replacement->save();

    //            $product_id = $request->product_id[$i];
    //            $check_previous_stock = Stock::where('product_id',$product_id)->latest()->pluck('current_stock')->first();
    //            if(!empty($check_previous_stock)){
    //                $previous_stock = $check_previous_stock;
    //            }else{
    //                $previous_stock = 0;
    //            }
    //            // product stock
    //            $stock = new Stock();
    //            $stock->user_id = Auth::id();
    //            $stock->ref_id = $insert_id;
    //            $stock->store_id = $request->store_id;
    //            $stock->date = $request->date;
    //            $stock->product_id = $request->product_id[$i];
    //            $stock->stock_type = 'sale';
    //            $stock->previous_stock = $previous_stock;
    //            $stock->stock_in = 0;
    //            $stock->stock_out = $request->qty[$i];
    //            $stock->current_stock = $previous_stock - $request->qty[$i];
    //            $stock->save();
                }
            }


        Toastr::success('Product Sale Created Successfully', 'Success');
        return redirect()->route('productSaleReplacement.index');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
