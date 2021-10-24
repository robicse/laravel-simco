<?php

namespace App\Http\Controllers;

use App\InvoiceStock;
use App\Party;
use App\ProductPurchase;
use App\ProductPurchaseDetail;
use App\ProductPurchaseReturn;
use App\Profit;
use App\Stock;
use App\Store;
use App\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPurchaseReturnController extends Controller
{

//    function __construct()
//    {
//        $this->middleware('permission:product-purchase-return-list|product-purchase-return-create|product-purchase-return-edit|product-purchase-return-delete', ['only' => ['index','show','returnablePurchaseProduct','purchaseProductReturn']]);
//        $this->middleware('permission:product-purchase-return-create', ['only' => ['create','store']]);
//        $this->middleware('permission:product-purchase-return-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:product-purchase-return-delete', ['only' => ['destroy']]);
//    }

    public function index()
    {
        $productPurchaseReturns = ProductPurchaseReturn::latest('id','desc')->get();
        //dd($productPurchaseReturns);
        return view('backend.productPurchaseReturn.index',compact('productPurchaseReturns'));
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
    public function returnablePurchaseProduct(){

        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $parties = Party::where('type','customer')->get() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $productPurchases = ProductPurchase::latest()->get();

        return view('backend.productPurchaseReturn.returnable_purchase_products',compact('parties','stores','productPurchases'));
    }

    public function getReturnablePurchaseProduct($purchase_id){
        $productPurchase = ProductSale::where('id',$purchase_id)->first();
        //dd($productPurchase);
        $products = DB::table('product_purchase_details')
            ->join('products','product_purchase_details.product_id','=','products.id')
            ->where('product_purchase_details.product_sale_id',$purchase_id)
            ->select('product_purchase_details.id','product_purchase_details.product_id','product_purchase_details.qty','product_purchase_details.price','product_purchase_details.discount','products.name')
            ->get();

        $html = "<table class=\"table table-striped tabel-penjualan\">
                        <thead>
                            <tr>
                                <th width=\"30\">No</th>
                                <th>Product Name</th>
                                <th align=\"right\">Received Quantity</th>
                                <th>Already Return Quantity</th>
                                <th>Return Quantity</th>
                                <th>Discount Amount</th>
                                <th>Amount</th>
                                <th>Reason <span style=\"color:red\">*</span></th>
                            </tr>
                        </thead>
                        <tbody>";
        if(count($products) > 0):
            foreach($products as $key => $item):

                $check_sale_return_qty = check_sale_return_qty($productPurchase->store_id,$item->product_id,$productPurchase->invoice_no);

                $key += 1;
                $html .= "<tr>";
                $html .= "<th width=\"30\">1</th>";
                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_id[]\" id=\"product_id_$key\" value=\"$item->product_id\" size=\"28\" /><input type=\"hidden\" class=\"form-control\" name=\"product_sale_detail_id[]\" id=\"product_sale_detail_id_$key\" value=\"$item->id\" size=\"28\" />$item->name</th>";
//                $html .= "<th><input type=\"hidden\" class=\"form-control\" name=\"product_sale_id[]\" id=\"product_sale_id_$key\" value=\"$item->product_sale_id\" size=\"28\" /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"qty[]\" id=\"qty_$key\" value=\"$item->qty\" size=\"28\" readonly /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"check_sale_return_qty[]\" id=\"check_sale_return_qty_$key\" value=\"$check_sale_return_qty\" readonly /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"return_qty[]\" id=\"return_qty_$key\" onkeyup=\"return_qty($key,this);\" size=\"28\" /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"discount[]\" id=\"discount_$key\"  value=\"$item->discount\" size=\"28\" /></th>";
                $html .= "<th><input type=\"text\" class=\"form-control\" name=\"total_amount[]\" id=\"total_amount_$key\"  value=\"$item->price\" size=\"28\" /></th>";
                $html .= "<th><textarea type=\"text\" class=\"form-control\" name=\"reason[]\" id=\"reason_$key\"  size=\"28\"></textarea> </th>";
                $html .= "</tr>";
            endforeach;
            $html .= "<tr>";

            $html .= "<th colspan=\"2\"><select name=\"payment_type\" id=\"payment_type\" class=\"form-control\" onchange=\"productType('')\" >
                    <option value=\"Cash\" selected>Cash</option>
                    <option value=\"Check\">Check</option>
            </select> </th>";
            $html .= "<th><input type=\"text\" name=\"check_number\" id=\"check_number\" class=\"form-control\" placeholder=\"Check Number\" readonly=\"readonly\"  size=\"28\" ></th>";
            $html .= "<th><input type=\"text\" name=\"discount_amount\" id=\"discount_amount\" class=\"form-control\" value=\"$productPurchase->discount_amount\" readonly=\"readonly\"  size=\"28\" ></th>";
            $html .= "</tr>";
        endif;
        $html .= "</tbody>
                    </table>";
        echo json_encode($html);
        //dd($html);
    }

    public function purchaseProductReturn(Request $request){
        //dd($request->all());
        $row_count = count($request->return_qty);
        $productSale = ProductSale::where('id',$request->product_sale_id)->first();


        $total_amount = 0;
        for ($i = 0; $i < $row_count; $i++) {
            if ($request->return_qty[$i] != null) {
                $total_amount += $request->total_amount[$i]*$request->return_qty[$i];
            }
        }

        $total_discount_amount = 0;
        for ($i = 0; $i < $row_count; $i++) {
            if ($request->return_qty[$i] != null) {
                $total_discount_amount += $request->discount[$i];
            }
        }

        $product_sale_return = new ProductSaleReturn();
        $product_sale_return->invoice_no = 'Salret-'.$productSale->invoice_no;
        $product_sale_return->sale_invoice_no = $productSale->invoice_no;
        $product_sale_return->product_sale_id = $productSale->id;
        $product_sale_return->user_id = Auth::id();
        $product_sale_return->store_id = $productSale->store_id;
        $product_sale_return->party_id = $productSale->party_id;
        $product_sale_return->payment_type = $productSale->payment_type;
        $product_sale_return->discount_type = $productSale->discount_type;
//        $product_sale_return->discount_amount = 0;
//        $product_sale_return->total_amount = $total_amount;
        $product_sale_return->discount_amount = $total_discount_amount;
        $product_sale_return->total_amount = $total_amount - $total_discount_amount;
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
                    $product_sale_return_detail->discount = $request->discount[$i];
                    $product_sale_return_detail->reason = isset($request->reason[$i]) ? $request->reason[$i] : 'Something Wrong';
                    $product_sale_return_detail->save();

                    $product_id = $productSaleDetail->product_id;
                    $purchase_invoice_no = ProductSaleDetail::where('product_sale_id',$productSale->id)->where('product_id',$product_id)->pluck('purchase_invoice_no')->first();
                    //dd($purchase_invoice_no);

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
                    $invoice_stock->invoice_no = 'Salret-'.$productSale->invoice_no;
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
                    $profit->invoice_no ='Salret-'.$productSale->invoice_no;
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
            $transaction->invoice_no = 'Salret-' . $productSale->invoice_no;
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
