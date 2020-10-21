<?php

namespace App\Http\Controllers;

use App\Party;
use App\Product;
use App\ProductBrand;
use App\ProductCategory;
use App\ProductSale;
use App\ProductSubCategory;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $products = Product::where('product_type','Finish Goods')->get();
        //dd($products);
        return view('backend.productPosSale.create',compact('parties','stores','products','productCategories','productSubCategories','productBrands'));
    }
}
