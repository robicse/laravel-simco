<?php
function store_test($store_id){
    return $store_id + 3;
}

if (!function_exists('sum_purchase_price')) {
    function sum_purchase_price($store_id)
    {
        $sum_purchase_price = 0;
        $productPurchaseDetails = DB::table('product_purchase_details')
            ->join('product_purchases', 'product_purchases.id', '=', 'product_purchase_details.product_purchase_id')
            ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
            ->where('product_purchases.store_id', $store_id)
            //->where('product_purchases.ref_id',NULL)
            //->where('product_purchases.purchase_product_type','Finish Goods')
            ->groupBy('product_id')
            ->groupBy('product_category_id')
            ->groupBy('product_sub_category_id')
            ->groupBy('product_brand_id')
            ->get();

        if (!empty($productPurchaseDetails)) {
            foreach ($productPurchaseDetails as $key => $productPurchaseDetail) {
                $sum_purchase_price += $productPurchaseDetail->sub_total;
            }
        }

        return $sum_purchase_price;
    }
}

if (!function_exists('sum_sale_price')) {
    function sum_sale_price($store_id)
    {
        $sum_sale_price = 0;
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
                // sale
                $productSaleDetails = DB::table('product_sale_details')
                    ->select('product_id', 'product_category_id', 'product_sub_category_id', 'product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
                    ->where('product_id', $productPurchaseDetail->product_id)
                    ->where('product_category_id', $productPurchaseDetail->product_category_id)
                    ->where('product_sub_category_id', $productPurchaseDetail->product_sub_category_id)
                    ->where('product_brand_id', $productPurchaseDetail->product_brand_id)
                    ->groupBy('product_id')
                    ->groupBy('product_category_id')
                    ->groupBy('product_sub_category_id')
                    ->groupBy('product_brand_id')
                    ->first();

                if (!empty($productSaleDetails)) {
                    $sum_sale_price += $productSaleDetails->sub_total;
                }
            }
        }

        return $sum_sale_price;
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

if (!function_exists('loss_profit')) {
    function loss_profit($store_id,$start_date=null,$end_date=null)
    {
        $sum_purchase_price = 0;
        $sum_sale_price = 0;
        $sum_sale_return_price = 0;
        $sum_profit_amount = 0;

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

        if(!empty($productPurchaseDetails)){
            foreach($productPurchaseDetails as $key => $productPurchaseDetail){
                $purchase_average_price = $productPurchaseDetail->sub_total/$productPurchaseDetail->qty;
                $sum_purchase_price += $productPurchaseDetail->sub_total;

                // sale
                $productSaleDetails = DB::table('product_sale_details')
                    ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'), DB::raw('SUM(sub_total) as sub_total'))
                    ->where('product_id',$productPurchaseDetail->product_id)
                    ->where('product_category_id',$productPurchaseDetail->product_category_id)
                    ->where('product_sub_category_id',$productPurchaseDetail->product_sub_category_id)
                    ->where('product_brand_id',$productPurchaseDetail->product_brand_id)
                    ->groupBy('product_id')
                    ->groupBy('product_category_id')
                    ->groupBy('product_sub_category_id')
                    ->groupBy('product_brand_id')
                    ->first();

                if(!empty($productSaleDetails))
                {
                    $sale_total_qty = $productSaleDetails->qty;
                    $sum_sale_price += $productSaleDetails->sub_total;
                    $sale_average_price = $productSaleDetails->sub_total/ (int) $productSaleDetails->qty;

                    if($sale_total_qty > 0){
                        $amount = ($sale_average_price*$sale_total_qty) - ($purchase_average_price*$sale_total_qty);
                        if($amount > 0){
                            $sum_profit_amount += $amount;
                        }else{
                            $sum_profit_amount -= $amount;
                        }

                    }
                }

                // sale return

                $productSaleReturnDetails = DB::table('product_sale_return_details')
                    ->select('product_id','product_category_id','product_sub_category_id','product_brand_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(price) as price'))
                    ->where('product_id',$productPurchaseDetail->product_id)
                    ->where('product_category_id',$productPurchaseDetail->product_category_id)
                    ->where('product_sub_category_id',$productPurchaseDetail->product_sub_category_id)
                    ->where('product_brand_id',$productPurchaseDetail->product_brand_id)
                    ->groupBy('product_id')
                    ->groupBy('product_category_id')
                    ->groupBy('product_sub_category_id')
                    ->groupBy('product_brand_id')
                    ->first();

                if(!empty($productSaleReturnDetails))
                {
                    $sale_return_total_qty = $productSaleReturnDetails->qty;
                    $sale_return_total_amount = $productSaleReturnDetails->price;
                    $sum_sale_return_price += $productSaleReturnDetails->price;
                    $sale_return_average_price = $sale_return_total_amount/$productSaleReturnDetails->qty;

                    if($sale_return_total_qty > 0){
                        $amount = $sale_return_average_price - ($purchase_average_price*$sale_return_total_qty);
                        if($amount > 0){
                            $sum_profit_amount -= $amount;
                        }else{
                            $sum_profit_amount += $amount;
                        }
                    }
                }
            }
        }

//        $total_expense = 0;
//        if($start_date != NULL && $end_date != NULL){
//            $total_expense = \App\Expense::where('date','>=',$start_date)
//                ->where('date','<=',$end_date)
//                ->where('store_id',$store_id)
//                ->sum('amount');
//        }else{
//            $total_expense = \App\Expense::where('store_id',$store_id)->sum('amount');
//        }
//
//        $loss_profit = $sum_profit_amount - $total_expense;

        $loss_profit = $sum_profit_amount;

        return $loss_profit;
    }
}

?>
