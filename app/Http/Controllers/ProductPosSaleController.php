<?php

namespace App\Http\Controllers;

use App\Party;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductPurchaseDetail;
use App\ProductSale;
use App\ProductSubCategory;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;

class ProductPosSaleController extends Controller
{
    public function index()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $productPosSales = ProductSale::where('sale_type','pos')->latest()->get();
        }else{
            $productPosSales = ProductSale::where('sale_type','pos')->where('user_id',$auth_user_id)->get();
        }
        return view('backend.productPosSale.index',compact('productPosSales'));
    }

    public function create()
    {
        /* check cart with raw data */
//        Cart::add([
//            'id' => '293ad',
//            'name' => 'Product 1',
//            'qty' => 1,
//            'price' => 9.99,
//            'options' => [
//                'size' => 'large'
//            ]
//        ]);

//        Cart::add([
//            [
//                'id' => '293ad',
//                'name' => 'Product 1',
//                'qty' => 1,
//                'price' => 10.00
//            ],
//            [
//                'id' => '4832k',
//                'name' => 'Product 2',
//                'qty' => 1,
//                'price' => 10.00,
//                'options' => [
//                    'size' => 'large'
//                ]
//            ]
//        ]);


//        Cart::add([$product1, $product2]);
//        Cart::content();
//        Cart::total();
//        Cart::count();
        Cart::destroy();
//        dd(Cart::count());
//        echo Cart::count();
        /* check cart with raw data */



        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $parties = Party::where('type','customer')->get() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $productCategories = ProductCategory::all();
        $productSubCategories = ProductSubCategory::all();
        $productBrands = ProductBrand::all();
        //$products = Product::where('product_type','Finish Goods')->get();
        $products = DB::table('product_purchase_details')
            ->select('product_purchase_details.product_id','product_purchase_details.product_code')
            ->leftJoin('products','products.id','=','product_purchase_details.product_id')
            //->whereIn('transaction_type',['Purchase','Payment','Bill','Receive','Journal','Opening'])
            //->where('account_no',$request->account_no)
            //->Orderby('id','asc')
            ->groupBy('product_purchase_details.product_id')
            ->groupBy('product_purchase_details.product_code')
            //->groupBy('product_purchase_details.qty')
            //->groupBy('product_purchase_details.mrp_price')
            ->get();
        //dd($products);
        return view('backend.productPosSale.create',compact('parties','stores','products','productCategories','productSubCategories','productBrands'));
    }

    public function loadForm($discount, $total, $paid){
        $bayar = $total - ($discount / 100 * $total);
        $due = ($paid != 0) ? $paid - $bayar : 0;

        $data = array(
            "totalrp" => $total,
            "bayar" => $bayar,
            "bayarrp" => $bayar,
            "terbilang" => $bayar." Tk",
            "kembalirp" => $due,
            "kembaliterbilang" => $due." Tk"
        );
        return response()->json($data);
    }

    public function listData($id)
    {
//        $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
//            ->where('id_penjualan', '=', $id)
//            ->get();

        $detail = ProductPurchaseDetail::leftJoin('product_purchases', 'product_purchases.id', '=', 'product_purchase_details.product_purchase_id')
            ->where('id_penjualan', '=', $id)
            ->get();
        $no = 0;
        $data = array();
        $total = 0;
        $total_item = 0;
        foreach($detail as $list){
            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = $list->nama_produk;
            $row[] = "Tk. ".format_uang($list->harga_jual);
            $row[] = "<input type='number' class='form-control' name='jumlah_$list->id_penjualan_detail' value='$list->jumlah' onChange='changeCount($list->id_penjualan_detail)'>";
            $row[] = $list->diskon."%";
            $row[] = "Tk. ".format_uang($list->sub_total);
            $row[] = '<div class="btn-group">
                    <a onclick="deleteItem('.$list->id_penjualan_detail.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
            $data[] = $row;

            $total += $list->harga_jual * $list->jumlah;
            $total_item += $list->jumlah;
        }

        $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span>", "", "", "", "", "", "", "");

        $output = array("data" => $data);
        return response()->json($output);
    }







    public function selectedform($product_code){

        $baseurl = URL('/pos_insert');

        // One Way

//        $html = '<form name="form" id="form" action="'.$baseurl.'" method="post" enctype="multipart/form-data">
//                <input type="hidden" name="_token" value="'.csrf_token().'" />
//                    <table class="table table-striped tabel-penjualan">
//                        <thead>
//                            <tr>
//                                <th width="30">No</th>
//                                <th>Product Code</th>
//                                <th>Product Name</th>
//                                <th align="right">Price</th>
//                                <th>Quantity</th>
//                                <th align="right">Sub Total</th>
//                                <th>Action</th>
//                            </tr>
//                        </thead>
//                        <tbody></tbody>
//                    </table>
//                    <div class="row">
//                        <div class="col-md-8">
//
//                        </div>
//                        <div class="col-md-4">
//                            <div class="form-group row">
//                                <label for="totalrp" class="col-md-4 control-label">Sub Total</label>
//                                <div class="col-md-8">
//                                    <input type="text" class="form-control" id="totalrp" readonly>
//                                </div>
//                            </div>
//                            <div class="form-group row">
//                                <label for="member" class="col-md-4 control-label">Customer</label>
//                                <div class="col-md-8">
//                                    <div class="input-group">
//                                        <input id="member" type="text" class="form-control" name="member" value="0">
//                                        <span class="input-group-btn">
//                                          <button onclick="showMember()" type="button" class="btn btn-info">...</button>
//                                        </span>
//                                    </div>
//                                </div>
//                            </div>
//                            <div class="form-group row">
//                                <label for="diskon" class="col-md-4 control-label">Discount</label>
//                                <div class="col-md-8">
//                                    <input type="text" class="form-control" name="diskon" id="diskon" value="0">
//                                </div>
//                            </div>
//                            <div class="form-group row">
//                                <label for="bayarrp" class="col-md-4 control-label">Grand Total</label>
//                                <div class="col-md-8">
//                                    <input type="text" class="form-control" name="bayarrp" id="diskon" value="0" readonly>
//                                </div>
//                            </div>
//                            <div class="form-group row">
//                                <label for="diterima" class="col-md-4 control-label">Paid</label>
//                                <div class="col-md-8">
//                                    <input type="number" class="form-control" value="0" name="diterima" id="diterima">
//                                </div>
//                            </div>
//                            <div class="form-group row">
//                                <label for="kembali" class="col-md-4 control-label">Due</label>
//                                <div class="col-md-8">
//                                    <input type="text" class="form-control" id="kembali" value="0" readonly>
//                                </div>
//                            </div>
//                            <div class="box-footer">
//                                <button type="submit" class="btn btn-primary pull-right simpan"><i class="fa fa-floppy-o"></i> Save</button>
//                            </div>
//                        </div>
//                    </div>
//            </form>';



        $html = "<form name=\"form\" id=\"form\" action=\"".$baseurl."\" method=\"post\" enctype=\"multipart/form-data\">
                <input type=\"hidden\" name=\"_token\" value=\"".csrf_token()."\" />
                    <table class=\"table table-striped tabel-penjualan\">
                        <thead>
                            <tr>
                                <th width=\"30\">No</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th align=\"right\">Price</th>
                                <th>Quantity</th>
                                <th align=\"right\">Sub Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>";
        if(Cart::count() > 0):
            foreach(Cart::content() as $item):
                $html .= "<tr>";
                $html .= "<th width=\"30\">1</th>";
                $html .= "<th>".$item->options['product_code']."</th>";
                $html .= "<th>".$item->name."</th>";
                $html .= "<th align=\"right\">".$item->price."</th>";
                $html .= "<th><input type=\"number\" name=\"qty\" value=\"1\" size=\"28\" </th>";
                $html .= "<th align=\"right\">".$item->price."</th>";
                $html .= "<th><input type=\"button\" class=\"btn btn-warning\" name=\"remove\" id=\"remove\" size=\"28\" value=\"Remove\" onClick=\"deleteCart('$item->rowId')\" /></th>";
                $html .= "</tr>";
            endforeach;
            $html .= "<tr><th align=\"right\" colspan=\"7\"><input type=\"button\" class=\"btn btn-danger\" name=\"remove\" id=\"remove\" size=\"28\" value=\"Clear Item\" onClick=\"deleteAllCart()\" /></th></tr>";
        endif;
        $html .= "</tbody>
                    </table>
                    <div class=\"row\">
                        <div class=\"col-md-8\">

                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group row\">
                                <label for=\"totalrp\" class=\"col-md-4 control-label\">Sub Total</label>
                                <div class=\"col-md-8\">
                                    <input type=\"text\" class=\"form-control\" id=\"totalrp\" readonly>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"member\" class=\"col-md-4 control-label\">Customer</label>
                                <div class=\"col-md-8\">
                                    <div class=\"input-group\">
                                        <input id=\"member\" type=\"text\" class=\"form-control\" name=\"member\" value=\"0\">
                                        <span class=\"input-group-btn\">
                                          <button onclick=\"showMember()\" type=\"button\" class=\"btn btn-info\">...</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"diskon\" class=\"col-md-4 control-label\">Discount</label>
                                <div class=\"col-md-8\">
                                    <input type=\"text\" class=\"form-control\" name=\"diskon\" id=\"diskon\" value=\"0\">
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"bayarrp\" class=\"col-md-4 control-label\">Grand Total</label>
                                <div class=\"col-md-8\">
                                    <input type=\"text\" class=\"form-control\" name=\"bayarrp\" id=\"diskon\" value=\"0\" readonly>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"diterima\" class=\"col-md-4 control-label\">Paid</label>
                                <div class=\"col-md-8\">
                                    <input type=\"number\" class=\"form-control\" value=\"0\" name=\"diterima\" id=\"diterima\">
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label for=\"kembali\" class=\"col-md-4 control-label\">Due</label>
                                <div class=\"col-md-8\">
                                    <input type=\"text\" class=\"form-control\" id=\"kembali\" value=\"0\" readonly>
                                </div>
                            </div>
                            <div class=\"box-footer\">
                                <button type=\"submit\" class=\"btn btn-primary pull-right simpan\"><i class=\"fa fa-floppy-o\"></i> Save</button>
                            </div>
                        </div>
                    </div>
            </form>";
        echo json_encode($html);

    }

}
