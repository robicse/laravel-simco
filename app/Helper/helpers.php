<?php

use App\InvoiceStock;
use App\ProductPurchaseDetail;
use App\ProductSaleDetail;
use App\Stock;
use App\Profit;
use Illuminate\Support\Facades\DB;

function store_test($store_id){
    return $store_id + 3;
}

if (!function_exists('sum_finish_goods_purchase_price')) {
    function sum_finish_goods_purchase_price($store_id)
    {
        $sum_finish_goods_purchase_price = 0;
        $productPurchaseDetails = DB::table('product_purchase_details')
            ->join('product_purchases', 'product_purchases.id', '=', 'product_purchase_details.product_purchase_id')
            ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
            ->where('product_purchases.store_id', $store_id)
            //->where('product_purchases.ref_id',NULL)
            ->where('product_purchases.purchase_product_type','Finish Goods')
            ->groupBy('product_id')
            ->groupBy('product_category_id')
            ->groupBy('product_sub_category_id')
            ->groupBy('product_brand_id')
            ->get();

        if (!empty($productPurchaseDetails)) {
            foreach ($productPurchaseDetails as $key => $productPurchaseDetail) {
                $sum_finish_goods_purchase_price += $productPurchaseDetail->sub_total;
            }
        }

        return $sum_finish_goods_purchase_price;
    }
}

if (!function_exists('sum_raw_materials_price')) {
    function sum_raw_materials_price($store_id)
    {
        $sum_raw_materials_price = 0;
        $productPurchaseDetails = DB::table('product_purchase_details')
            ->join('product_purchases', 'product_purchases.id', '=', 'product_purchase_details.product_purchase_id')
            ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
            ->where('product_purchases.store_id', $store_id)
            //->where('product_purchases.ref_id',NULL)
            ->where('product_purchases.purchase_product_type','Raw Materials')
            ->groupBy('product_id')
            ->groupBy('product_category_id')
            ->groupBy('product_sub_category_id')
            ->groupBy('product_brand_id')
            ->get();

        $product_productions = DB::table('product_productions')
            ->select(DB::raw('SUM(total_amount) as sum_production_total_amount'))
            ->where('store_id',$store_id)
            ->first();

        $sum_production_total_amount = $product_productions->sum_production_total_amount;

        if (!empty($productPurchaseDetails)) {
            foreach ($productPurchaseDetails as $key => $productPurchaseDetail) {
                $sum_raw_materials_price += $productPurchaseDetail->sub_total;
            }
        }

        return $sum_raw_materials_price - $sum_production_total_amount;
    }
}

//if (!function_exists('sum_sale_price')) {
//    function sum_sale_price($store_id)
//    {
//        $sum_sale_price = 0;
//        $productPurchaseDetails = DB::table('product_purchase_details')
//            ->join('product_purchases','product_purchases.id','=','product_purchase_details.product_purchase_id')
//            ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
//            ->where('product_purchases.store_id',$store_id)
//            //->where('product_purchases.ref_id',NULL)
//            ->where('product_purchases.purchase_product_type','Finish Goods')
//            ->groupBy('product_id')
//            ->groupBy('product_category_id')
//            ->groupBy('product_sub_category_id')
//            ->groupBy('product_brand_id')
//            ->get();
//
//        if(!empty($productPurchaseDetails)) {
//            foreach ($productPurchaseDetails as $key => $productPurchaseDetail) {
//                // sale
//                $productSaleDetails = DB::table('product_sale_details')
//                    ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
//                    ->where('product_id', $productPurchaseDetail->product_id)
//                    ->where('product_category_id', $productPurchaseDetail->product_category_id)
//                    ->where('product_sub_category_id', $productPurchaseDetail->product_sub_category_id)
//                    ->where('product_brand_id', $productPurchaseDetail->product_brand_id)
//                    ->groupBy('product_id')
//                    ->groupBy('product_category_id')
//                    ->groupBy('product_sub_category_id')
//                    ->groupBy('product_brand_id')
//                    ->first();
//
//                if (!empty($productSaleDetails)) {
//                    $sum_sale_price += $productSaleDetails->sub_total;
//                }
//            }
//        }
//
//        return $sum_sale_price;
//    }
//}

if (!function_exists('sum_sale_price')) {
    function sum_sale_price($store_id)
    {
        $product_sales = DB::table('product_sales')
            ->select(DB::raw('SUM(total_amount) as sum_product_sale_amount'))
            ->where('store_id',$store_id)
            ->first();

        return $sum_sale_price = $product_sales->sum_product_sale_amount;

    }
}

if (!function_exists('sum_sale_return_price')) {
    function sum_sale_return_price($store_id)
    {
        $sum_sale_return_price = 0;
        $productPurchaseDetails = DB::table('product_purchase_details')
            ->join('product_purchases','product_purchases.id','=','product_purchase_details.product_purchase_id')
            ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
            ->where('product_purchases.store_id',$store_id)
            //->where('product_purchases.ref_id',NULL)
            //->where('product_purchases.purchase_product_type','Finish Goods')
            ->groupBy('product_id')
            ->groupBy('product_category_id')
            ->groupBy('product_sub_category_id')
            ->groupBy('product_brand_id')
            ->get();

        if(!empty($productPurchaseDetails)) {
            foreach ($productPurchaseDetails as $key => $productPurchaseDetail) {
                // sale return
                $productSaleReturnDetails = DB::table('product_sale_return_details')
                    ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'))
                    ->where('product_id', $productPurchaseDetail->product_id)
                    ->where('product_category_id', $productPurchaseDetail->product_category_id)
                    ->where('product_sub_category_id', $productPurchaseDetail->product_sub_category_id)
                    ->where('product_brand_id', $productPurchaseDetail->product_brand_id)
                    ->groupBy('product_id')
                    ->groupBy('product_category_id')
                    ->groupBy('product_sub_category_id')
                    ->groupBy('product_brand_id')
                    ->first();

                if (!empty($productSaleReturnDetails)) {
                    $sum_sale_return_price += $productSaleReturnDetails->price;

                }
            }
        }

        return $sum_sale_return_price;
    }
}

if (!function_exists('total_expense')) {
    function total_expense($store_id,$start_date=null,$end_date=null)
    {
        $total_expense = 0;
        if($start_date != NULL && $end_date != NULL){
            $total_expense = \App\Expense::where('date','>=',$start_date)
                ->where('date','<=',$end_date)
                ->where('store_id',$store_id)
                ->sum('amount');
        }else{
            $total_expense = \App\Expense::where('store_id',$store_id)->sum('amount');
        }

        return $total_expense;
    }
}

if (!function_exists('product_sale_discount')) {
    function product_sale_discount($store_id)
    {
        $productSaleDiscount = DB::table('product_sales')
            ->select( DB::raw('SUM(discount_amount) as total_discount'))
            ->first();
        $sum_total_discount = 0;
        if($productSaleDiscount){
            $sum_total_discount = $productSaleDiscount->total_discount;
        }

        return $sum_total_discount;
    }
}

//if (!function_exists('loss_profit')) {
//    function loss_profit($store_id,$start_date=null,$end_date=null)
//    {
//        $sum_purchase_price = 0;
//        $sum_sale_price = 0;
//        $sum_sale_return_price = 0;
//        $sum_profit_amount = 0;
//
//        $productPurchaseDetails = DB::table('product_purchase_details')
//            ->join('product_purchases','product_purchases.id','=','product_purchase_details.product_purchase_id')
//            ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
//            ->where('product_purchases.store_id',$store_id)
//            ->where('product_purchases.created_at','>=',$start_date.' 00:00:00')
//            ->where('product_purchases.created_at','>=',$end_date.' 23:59:59')
//            //->where('product_purchases.ref_id',NULL)
//            //->where('product_purchases.purchase_product_type','Finish Goods')
//            ->groupBy('product_id')
//            ->groupBy('product_category_id')
//            ->groupBy('product_sub_category_id')
//            ->groupBy('product_brand_id')
//            ->get();
//
//        if(!empty($productPurchaseDetails)){
//            foreach($productPurchaseDetails as $key => $productPurchaseDetail){
//                $purchase_average_price = $productPurchaseDetail->sub_total/$productPurchaseDetail->qty;
//                $sum_purchase_price += $productPurchaseDetail->sub_total;
//
//                // sale
//                $productSaleDetails = DB::table('product_sale_details')
//                    ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
//                    ->where('product_id',$productPurchaseDetail->product_id)
//                    ->where('product_category_id',$productPurchaseDetail->product_category_id)
//                    ->where('product_sub_category_id',$productPurchaseDetail->product_sub_category_id)
//                    ->where('product_brand_id',$productPurchaseDetail->product_brand_id)
//                    ->groupBy('product_id')
//                    ->groupBy('product_category_id')
//                    ->groupBy('product_sub_category_id')
//                    ->groupBy('product_brand_id')
//                    ->first();
//
//                if(!empty($productSaleDetails))
//                {
//                    $sale_total_qty = $productSaleDetails->qty;
//                    $sum_sale_price += $productSaleDetails->sub_total;
//                    $sale_average_price = $productSaleDetails->sub_total/ (int) $productSaleDetails->qty;
//
//                    if($sale_total_qty > 0){
//                        $amount = ($sale_average_price*$sale_total_qty) - ($purchase_average_price*$sale_total_qty);
//                        if($amount > 0){
//                            $sum_profit_amount += $amount;
//                        }else{
//                            $sum_profit_amount -= $amount;
//                        }
//
//                    }
//                }
//
//                // sale return
//
//                $productSaleReturnDetails = DB::table('product_sale_return_details')
//                    ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'))
//                    ->where('product_id',$productPurchaseDetail->product_id)
//                    ->where('product_category_id',$productPurchaseDetail->product_category_id)
//                    ->where('product_sub_category_id',$productPurchaseDetail->product_sub_category_id)
//                    ->where('product_brand_id',$productPurchaseDetail->product_brand_id)
//                    ->groupBy('product_id')
//                    ->groupBy('product_category_id')
//                    ->groupBy('product_sub_category_id')
//                    ->groupBy('product_brand_id')
//                    ->first();
//
//                if(!empty($productSaleReturnDetails))
//                {
//                    $sale_return_total_qty = $productSaleReturnDetails->qty;
//                    $sale_return_total_amount = $productSaleReturnDetails->price;
//                    $sum_sale_return_price += $productSaleReturnDetails->price;
//                    $sale_return_average_price = $sale_return_total_amount/$productSaleReturnDetails->qty;
//
//                    if($sale_return_total_qty > 0){
//                        $amount = $sale_return_average_price - ($purchase_average_price*$sale_return_total_qty);
//                        if($amount > 0){
//                            $sum_profit_amount -= $amount;
//                        }else{
//                            $sum_profit_amount += $amount;
//                        }
//                    }
//                }
//            }
//        }
//
//        $loss_profit = $sum_profit_amount;
//
//        return $loss_profit;
//    }
//}

if (!function_exists('loss_profit')) {
    function loss_profit($store_id,$start_date=null,$end_date=null)
    {
        if($start_date != null && $end_date != null){
             $profit = DB::table('profits')
                ->select(DB::raw('SUM(profit_amount) as sum_profit_amount'))
                ->where('store_id',$store_id)
                ->where('date','>=',$start_date)
                ->where('date','<=',$end_date)
                ->first();

            return $loss_profit = $profit->sum_profit_amount;
        }else{
            $profit = DB::table('profits')
                ->select(DB::raw('SUM(profit_amount) as sum_profit_amount'))
                ->where('store_id',$store_id)
                ->first();

            return $loss_profit = $profit->sum_profit_amount;
        }

    }
}

if (!function_exists('party_discounts')) {
    function party_discounts($store_id=null,$party_id=null,$start_date=null,$end_date=null)
    {
        if($party_id != null && $start_date != null && $end_date != null){
            return $party_discounts = DB::table('product_sales')
                ->join('parties','product_sales.party_id','parties.id')
                ->where('product_sales.discount_amount','>',0)
                ->where('product_sales.store_id',$store_id)
                ->where('parties.id',$party_id)
                ->where('product_sales.date','>=',$start_date)
                ->where('product_sales.date','<=',$end_date)
                ->select('product_sales.invoice_no','product_sales.discount_amount','product_sales.date','parties.name')
                ->get();
        }elseif($party_id != null){
            return $party_discounts = DB::table('product_sales')
                ->join('parties','product_sales.party_id','parties.id')
                ->where('product_sales.discount_amount','>',0)
                ->where('product_sales.store_id',$store_id)
                ->where('parties.id',$party_id)
                ->select('product_sales.invoice_no','product_sales.discount_amount','product_sales.date','parties.name')
                ->get();
        }elseif($start_date != null && $end_date != null){
            return $party_discounts = DB::table('product_sales')
                ->join('parties','product_sales.party_id','parties.id')
                ->where('product_sales.discount_amount','>',0)
                ->where('product_sales.store_id',$store_id)
                ->where('product_sales.date','>=',$start_date)
                ->where('product_sales.date','<=',$end_date)
                ->select('product_sales.invoice_no','product_sales.discount_amount','product_sales.date','parties.name')
                ->get();
        }else{
            return $party_discounts = DB::table('product_sales')
                ->join('parties','product_sales.party_id','parties.id')
                ->where('product_sales.discount_amount','>',0)
                ->where('product_sales.store_id',$store_id)
                ->select('product_sales.invoice_no','product_sales.discount_amount','product_sales.date','parties.name')
                ->get();
        }
    }
}

if (!function_exists('purchase_invoice_nos')) {
    function purchase_invoice_nos($store_id=null,$product_id=null)
    {
        if($store_id != null && $product_id != null){
            return $invoice_nos = DB::table('product_purchases')
                ->leftjoin('product_purchase_details','product_purchase_details.product_purchase_id','product_purchases.id')
                ->where('product_purchase_details.qty_stock_status','Available')
                ->where('product_purchases.purchase_product_type','Finish Goods')
                ->where('product_purchases.store_id',$store_id)
                ->where('product_purchase_details.product_id',$product_id)
                ->select('product_purchases.invoice_no')
                ->get();
        }else{
            return $invoice_nos = DB::table('product_purchases')
                ->leftjoin('product_purchase_details','product_purchase_details.product_purchase_id','product_purchases.id')
                ->where('product_purchase_details.qty_stock_status','Available')
                ->where('product_purchases.purchase_product_type','Finish Goods')
                ->select('product_purchases.invoice_no')
                ->get();
        }
    }
}

if (!function_exists('current_stock_row')) {
    function current_stock_row($store_id,$stock_product_type,$stock_type,$product_id)
    {
        return $current_stock_row = Stock::where('store_id',$store_id)
            ->where('stock_type',$stock_type)
            ->where('stock_product_type',$stock_product_type)
            ->where('product_id',$product_id)
            ->latest()->first();
    }
}


if (!function_exists('current_invoice_stock_row')) {
    function current_invoice_stock_row($store_id,$stock_product_type,$stock_type,$product_id,$purchase_invoice_no,$invoice_no=null)
    {
        if($invoice_no != null){
            return $current_stock_row = InvoiceStock::where('store_id',$store_id)
                ->where('stock_type',$stock_type)
                ->where('stock_product_type',$stock_product_type)
                ->where('purchase_invoice_no',$purchase_invoice_no)
                ->where('invoice_no',$invoice_no)
                ->where('product_id',$product_id)
                ->first();

        }else{
            return $current_stock_row = InvoiceStock::where('store_id',$store_id)
                ->where('stock_type',$stock_type)
                ->where('stock_product_type',$stock_product_type)
                ->where('purchase_invoice_no',$purchase_invoice_no)
                ->where('product_id',$product_id)
                ->first();
        }
    }
}

if (!function_exists('get_profit_amount')) {
    function get_profit_amount($purchase_invoice_no,$product_id)
    {
        return $get_profit_amount = ProductPurchaseDetail::where('invoice_no',$purchase_invoice_no)
            ->where('product_id',$product_id)
            ->pluck('profit_amount')
            ->first();
    }
}

if (!function_exists('get_profit_amount_row')) {
    function get_profit_amount_row($store_id,$purchase_invoice_no,$invoice_no,$product_id)
    {
        return $get_profit_amount = Profit::where('store_id',$store_id)
            ->where('purchase_invoice_no',$purchase_invoice_no)
            ->where('invoice_no',$invoice_no)
            ->where('product_id',$product_id)
            ->first();
    }
}

if (!function_exists('edited_current_invoice_stock')) {
    function edited_current_invoice_stock($store_id,$purchase_invoice_no,$product_id,$invoice_no,$product_sale_detail_id)
    {
        $purchase_qty = DB::table('product_purchase_details')
            ->join('product_purchases','product_purchase_details.product_purchase_id','product_purchases.id')
            ->where('product_purchases.store_id',$store_id)
            ->where('product_purchases.invoice_no',$purchase_invoice_no)
            ->where('product_purchase_details.product_id',$product_id)
            ->pluck('product_purchase_details.qty')
            ->first();

        $previous_sale_qty = DB::table('product_sale_details')
            ->join('product_sales','product_sale_details.product_sale_id','product_sales.id')
            ->where('product_sales.store_id',$store_id)
            ->where('product_sale_details.purchase_invoice_no',$purchase_invoice_no)
            ->where('product_sales.invoice_no','!=',$invoice_no)
            ->where('product_sale_details.product_id',$product_id)
            ->select(DB::raw('SUM(product_sale_details.qty) as sum_qty'))
            ->first();
        $previous_sale_sum_qty = $previous_sale_qty->sum_qty;
        if($previous_sale_sum_qty != null){
            return $purchase_qty - $previous_sale_sum_qty;
        }else{
            return $purchase_qty;
        }

        //$current_qty = ProductSaleDetail::where('id',$product_sale_detail_id)->pluck('qty')->first();
    }
}

?>