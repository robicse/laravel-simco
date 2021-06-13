<?php

namespace App\Http\Controllers;

use App\InvoiceStock;
use App\Party;
use App\ProductPurchase;
use App\ProductPurchaseDetail;
use App\Profit;
use App\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\ProductSale;
use App\ProductSaleDetail;
use App\ProductSaleReturn;
use App\ProductSaleReturnDetail;
use App\Transaction;
use App\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSaleReturnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-sale-return-list|product-sale-return-create|product-sale-return-edit|product-sale-return-delete', ['only' => ['index','show','returnableSaleProduct','saleProductReturn']]);
        $this->middleware('permission:product-sale-return-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-sale-return-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-sale-return-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $productSaleReturns = ProductSaleReturn::latest('id','desc')->get();
        //dd($productSaleReturns);
        return view('backend.productSaleReturn.index',compact('productSaleReturns'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $productSaleReturn = ProductSaleReturn::find($id);
        $productSaleReturnDetails = ProductSaleReturnDetail::where('product_sale_return_id',$id)->get();

        return view('backend.productSaleReturn.show', compact('productSaleReturn','productSaleReturnDetails'));
    }

    public function edit($id)
    {
        $productSaleReturn = ProductSaleReturn::find($id);
        $productSaleReturnDetails = ProductSaleReturnDetail::where('product_sale_return_id',$id)->get();

        return view('backend.productSaleReturn.edit_returnable_sale_products', compact('productSaleReturn','productSaleReturnDetails'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());

        $count = count($request->qty);
        $total_amount = 0;

        for($i=0; $i<$count; $i++){
            $product_sale_return_detail_id = $request->product_sale_return_detail_id[$i];
            $productSaleReturnDetail = ProductSaleReturnDetail::find($product_sale_return_detail_id);
            $productSaleReturnDetail->qty = $request->qty[$i];
            $productSaleReturnDetail->price = $request->price[$i];
            $productSaleReturnDetail->reason = $request->reason[$i];
            $productSaleReturnDetail->update();

            $total_amount += $request->price[$i]*$request->price[$i];
            $request_qty = $request->qty[$i];



            $product_id = $productSaleReturnDetail->product_id;
            $product_sale_return = ProductSaleReturn::find($request->product_sale_return_id);
            $invoice_no = $product_sale_return->invoice_no;
            $sale_invoice_no = $product_sale_return->sale_invoice_no;
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


            // product stock
            $store_id=$product_sale_return->store_id;
            $stock_row = current_stock_row($store_id,'Finish Goods','sale return',$product_id);
            $previous_stock = $stock_row->previous_stock;
            $stock_out = $stock_row->stock_out;
            //$current_stock = $stock_row->current_stock;


            if($stock_out != $request_qty){
                $stock_row->user_id = Auth::id();
                $stock_row->store_id = $store_id;
                $stock_row->product_id = $product_id;
                $stock_row->previous_stock = $previous_stock;
                $stock_row->stock_in = $request_qty;
                $stock_row->stock_out = 0;
                $new_stock_out = $previous_stock + $request_qty;
                $stock_row->current_stock = $new_stock_out;
                $stock_row->update();
            }



            // invoice stock
            $invoice_stock_row = current_invoice_stock_row($store_id,'Finish Goods','sale return',$product_id,$purchase_invoice_no,$invoice_no);
            $previous_invoice_stock = $invoice_stock_row->previous_stock;
            $invoice_stock_out = $invoice_stock_row->stock_out;

            if($invoice_stock_out != $request_qty){
                $invoice_stock_row->user_id = Auth::id();
                $invoice_stock_row->store_id = $store_id;
                $invoice_stock_row->date = date('Y-m-d');
                $invoice_stock_row->product_id = $product_id;
                $invoice_stock_row->previous_stock = $previous_invoice_stock;
                $invoice_stock_row->stock_in = $request_qty;
                $invoice_stock_row->stock_out = 0;
                $new_stock_out = $previous_invoice_stock + $request_qty;
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

        $productSaleReturn = ProductSaleReturn::find($request->product_sale_return_id);
        $productSaleReturn->total_amount = $total_amount;
        $productSaleReturn->update();




        // transaction
        $transaction = Transaction::where('invoice_no',$productSaleReturn->invoice_no)->first();
        $transaction->user_id = Auth::id();
        $transaction->store_id = $store_id;
        //$transaction->payment_type = $request->payment_type;
        //$transaction->check_number = $request->check_number;
        $transaction->date = date('Y-m-d');
        $transaction->amount = $total_amount;
        $transaction->save();

        Toastr::success('Product Sale Return Updated Successfully', 'Success');
        return redirect()->route('productSaleReturns.index');
    }

    public function destroy($id)
    {
        $productSaleReturn = ProductSaleReturn::find($id);
        $productSaleReturn->delete();

        DB::table('product_sale_return_details')->where('product_sale_return_id',$id)->delete();
        DB::table('stocks')->where('ref_id',$id)->where('stock_type','sale return')->delete();
        DB::table('transactions')->where('ref_id',$id)->where('transaction_type','sale return')->delete();

        Toastr::success('Product Sale Return Deleted Successfully', 'Success');
        return redirect()->route('productSaleReturns.index');
    }

    public function returnableSaleProduct(){
//        $returnable_sale_products = ProductSaleDetail::where('return_type','returnable')->get();
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $parties = Party::where('type','customer')->get() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $productSales = ProductSale::latest()->get();

        //dd($returnable_sale_products);
        //return view('backend.productSaleReturn.returnable_sale_products',compact('returnable_sale_products'));
        return view('backend.productSaleReturn.returnable_sale_products',compact('parties','stores','productSales'));
    }
    public function getReturnableProduct($sale_id){
        //$products = ProductSaleDetail::where('product_sale_id',$sale_id)->get();
        $products = DB::table('product_sale_details')
            ->join('products','product_sale_details.product_id','=','products.id')
            ->where('product_sale_details.product_sale_id',$sale_id)
            ->select('product_sale_details.id','product_sale_details.product_id','product_sale_details.qty','product_sale_details.price','products.name')
            ->get();

        $html = "<table class=\"table table-striped tabel-penjualan\">
                        <thead>
                            <tr>
                                <th width=\"30\">No</th>
                                <th>Product Name</th>
                                <th align=\"right\">Received Quantity</th>
                                <th>Return Quantity</th>
                                <th>Amount</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>";
        if(count($products) > 0):
            foreach($products as $key => $item):
                $key += 1;
                $html .= "<tr>";
                $html .= "<th width=\"30\">1</th>";
                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_id[]\" id=\"product_id_$key\" value=\"$item->product_id\" size=\"28\" /><input type=\"hidden\" class=\"form-control\" name=\"product_sale_detail_id[]\" id=\"product_sale_detail_id_$key\" value=\"$item->id\" size=\"28\" />$item->name</th>";
//                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_sale_id[]\" id=\"product_sale_id_$key\" value=\"$item->product_sale_id\" size=\"28\" /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"qty[]\" id=\"qty_$key\" value=\"$item->qty\" size=\"28\" readonly /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"return_qty[]\" id=\"return_qty_$key\" onkeyup=\"return_qty($key,this);\" size=\"28\" /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"total_amount[]\" id=\"total_amount_$key\"  value=\"$item->price\" size=\"28\" /></th>";
                $html .= "<th><textarea type=\"text\" class=\"form-control\" name=\"reason[]\" id=\"reason_$key\"  size=\"28\" ></textarea> </th>";
                $html .= "</tr>";
            endforeach;
            $html .= "<tr>";
            $html .= "<th colspan=\"2\"><select name=\"payment_type\" id=\"payment_type\" class=\"form-control\" onchange=\"productType('')\" >
                    <option value=\"Cash\" selected>Cash</option>
                    <option value=\"Check\">Check</option>
            </select> </th>";
            $html .= "<th><input type=\"text\" name=\"check_number\" id=\"check_number\" class=\"form-control\" placeholder=\"Check Number\" readonly=\"readonly\"  size=\"28\" ></th>";
            $html .= "</tr>";
        endif;
        $html .= "</tbody>
                    </table>";
        echo json_encode($html);
        //dd($html);
    }
    public function saleProductReturn(Request $request){
        //dd($request->all());
        $row_count = count($request->return_qty);
        $productSale = ProductSale::where('id',$request->product_sale_id)->first();
        $purchase_invoice_no = ProductSaleDetail::where('product_sale_id',$productSale->id)->pluck('purchase_invoice_no')->first();
        //dd($row_count);

        $total_amount = 0;
        for ($i = 0; $i < $row_count; $i++) {
            if ($request->return_qty[$i] != null) {
                $total_amount += $request->total_amount[$i];
            }
        }
        $product_sale_return = new ProductSaleReturn();
        $product_sale_return->invoice_no = 'salret-'.$productSale->invoice_no;
        $product_sale_return->sale_invoice_no = $productSale->invoice_no;
        $product_sale_return->product_sale_id = $productSale->id;
        $product_sale_return->user_id = Auth::id();
        $product_sale_return->store_id = $productSale->store_id;
        $product_sale_return->party_id = $productSale->party_id;
        $product_sale_return->payment_type = $productSale->payment_type;
        $product_sale_return->discount_type = $productSale->discount_type;
        $product_sale_return->discount_amount = 0;
        $product_sale_return->total_amount = $total_amount;
        $product_sale_return->save();

        $insert_id = $product_sale_return->id;
        if($insert_id) {
            for ($i = 0; $i < $row_count; $i++) {
                if ($request->return_qty[$i] != null) {
                    $product_sale_detail_id = $request->product_sale_detail_id[$i];
                    $productSaleDetail = ProductSaleDetail::where('id',$product_sale_detail_id)->first();

                    $product_sale_return_detail = new ProductSaleReturnDetail();
                    $product_sale_return_detail->product_sale_return_id = $insert_id;
                    $product_sale_return_detail->product_sale_detail_id = $productSaleDetail->id;
                    $product_sale_return_detail->product_category_id = $productSaleDetail->product_category_id;
                    $product_sale_return_detail->product_sub_category_id = $productSaleDetail->product_sub_category_id;
                    $product_sale_return_detail->product_brand_id = $productSaleDetail->product_brand_id;
                    $product_sale_return_detail->product_id = $productSaleDetail->product_id;
                    $product_sale_return_detail->qty = $request->return_qty[$i];
                    $product_sale_return_detail->price = $request->total_amount[$i];
                    $product_sale_return_detail->reason = $request->reason[$i];
                    $product_sale_return_detail->save();

                    $product_id = $productSaleDetail->product_id;

                    // update purchase details table stock status
                    $product_purchase_details_info = ProductPurchaseDetail::where('invoice_no',$purchase_invoice_no)->where('product_id',$product_id)->first();
                    $purchase_qty = $product_purchase_details_info->qty;
                    $purchase_previous_sale_qty = $product_purchase_details_info->sale_qty;
                    $total_sale_qty = $purchase_previous_sale_qty - $request->return_qty[$i];
                    $product_purchase_details_info->sale_qty = $total_sale_qty;
                    if($total_sale_qty == $purchase_qty){
                        $product_purchase_details_info->qty_stock_status = 'Not Available';
                    }else{
                        $product_purchase_details_info->qty_stock_status = 'Available';
                    }
                    $product_purchase_details_info->save();


                    $check_previous_stock = Stock::where('product_id', $product_id)->latest()->pluck('current_stock')->first();
                    if (!empty($check_previous_stock)) {
                        $previous_stock = $check_previous_stock;
                    } else {
                        $previous_stock = 0;
                    }

                    // product stock
                    $stock = new Stock();
                    $stock->user_id = Auth::id();
                    $stock->ref_id = $insert_id;
                    $stock->store_id = $productSale->store_id;
                    $stock->product_id = $product_id;
                    $stock->stock_type = 'sale return';
                    $stock->previous_stock = $previous_stock;
                    $stock->stock_in = $request->return_qty[$i];
                    $stock->stock_out = 0;
                    $stock->current_stock = $previous_stock + $request->return_qty[$i];
                    $stock->date = date('Y-m-d');
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
                    $invoice_stock->invoice_no = 'salret-'.$productSale->invoice_no;
                    $invoice_stock->store_id = $productSale->store_id;
                    $invoice_stock->date = $request->date;
                    $invoice_stock->product_id = $product_id;
                    $invoice_stock->stock_type = 'sale return';
                    $invoice_stock->previous_stock = $previous_invoice_stock;
                    $invoice_stock->stock_in = $request->return_qty[$i];
                    $invoice_stock->stock_out = 0;
                    $invoice_stock->current_stock = $previous_invoice_stock + $request->return_qty[$i];
                    $invoice_stock->save();


                    $profit_amount = get_profit_amount($purchase_invoice_no,$product_id);

                    // profit table
                    $profit = new Profit();
                    $profit->ref_id = $insert_id;
                    $profit->purchase_invoice_no = $purchase_invoice_no;
                    $profit->invoice_no ='salret-'.$productSale->invoice_no;
                    $profit->user_id = Auth::id();
                    $profit->store_id = $productSale->store_id;
                    $profit->type = 'Sale';
                    $profit->product_id = $product_id;
                    $profit->qty = $request->return_qty[$i];
                    $profit->price = $request->total_amount[$i];
                    $profit->sub_total = $request->return_qty[$i]*$request->total_amount[$i];
                    $profit->discount_amount = 0;
                    $profit->profit_amount = -($profit_amount*$request->return_qty[$i]);
                    $profit->date = date('Y-m-d');
                    $profit->save();

                }
            }

            $transaction_product_type = Transaction::where('invoice_no',$productSale->invoice_no)->pluck('transaction_product_type')->first();

            // transaction
            $transaction = new Transaction();
            $transaction->invoice_no = 'salret-' . $productSale->invoice_no;
            $transaction->user_id = Auth::id();
            $transaction->store_id = $productSale->store_id;
            $transaction->party_id = $productSale->party_id;
            $transaction->ref_id = $insert_id;
            $transaction->transaction_product_type = $transaction_product_type;
            $transaction->transaction_type = 'sale return';
            $transaction->payment_type = $request->payment_type;
            $transaction->check_number = $request->check_number;
            $transaction->date = date('Y-m-d');
            $transaction->amount = $total_amount;
            $transaction->save();
        }

        Toastr::success('Product Sale Return Created Successfully', 'Success');
        return redirect()->route('productSaleReturns.index');
    }
}
