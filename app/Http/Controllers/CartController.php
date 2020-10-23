<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductPurchaseDetail;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product_code = $request->product_code;
        $data = array();
        if($product_code){
            $product_check_exists = Product::where('product_code',$product_code)->pluck('id')->first();
            if($product_check_exists){
                $data['product_check_exists'] = 'Product Found!';
                $product = DB::table('products')
                    ->where('product_code',$product_code)
                    ->first();

                if(!empty($product)){
                    $price = ProductPurchaseDetail::where('product_id',$product->id)->latest()->pluck('mrp_price')->first();

                    $data['id'] = $product->id;
                    $data['name'] = $product->name;
                    $data['qty'] = 1;
                    $data['price'] = $price;
                    $data['options']['product_code'] = $product_code;
                    Cart::add($data);
                }
                $data['countCart'] = Cart::count();
            }else{
                $data['product_check_exists'] = 'No Product Found!';
            }

        }
        return response()->json(['success'=> true, 'response'=>$data]);
    }

    public function deleteCartProduct($rowId){
        if($rowId){
            Cart::remove($rowId);
        }
        $info['success'] = true;
        echo json_encode($info);
    }

    public function deleteAllCartProduct(){

        Cart::destroy();
        $info['success'] = true;
        echo json_encode($info);
    }
}
