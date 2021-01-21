<?php

namespace App\Http\Controllers;

use App\Exports\StockExport;
use App\Exports\TransactionExport;
use App\Product;
use App\ProductPurchase;
use App\ProductPurchaseDetail;
use App\Stock;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['stockList']]);
    }
    public function stockList(){

        $stock_product_type = Input::get('stock_product_type') ? Input::get('stock_product_type') : '';
        $stock_type = Input::get('stock_type') ? Input::get('stock_type') : '';
        $product_id = Input::get('product_id') ? Input::get('product_id') : '';

        $products = Product::all();
        $stores = Store::latest()->get();

        return view('backend.stock.index', compact('stores','products','stock_product_type','stock_type','product_id'));
    }
    public function export()
    {
        //return Excel::download(new UsersExport, 'users.xlsx');
        return Excel::download(new StockExport, 'stock.xlsx');
    }

    public function stockSummaryList(){
        $stores = Store::latest()->get();
        return view('backend.stock.stock_summary', compact('stores'));
    }

    public function stockLowList(){
        $stores = Store::latest()->get();
        return view('backend.stock.stock_low', compact('stores'));
    }
}
