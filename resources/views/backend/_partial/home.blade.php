@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            </ul>

        </div>
        <div class="row">
            @if(Auth::User()->getRoleNames()[0] == "Admin")
                @if(!empty($stores))
                    @foreach($stores as $store)
                        <div class="col-md-12">
                            <h1 class="text-center">{{$store->name}}</h1>
                        </div>

{{--                        <div class="col-md-12">--}}
{{--                            <h6>Total Purchase: {{number_format($sum_purchase_price, 2, '.', '')}}</h6>--}}
{{--                            <h6>Total Sale: {{number_format($sum_sale_price, 2, '.', '')}}</h6>--}}
{{--                            <h6>Purchase Sale Profit:{{$sum_purchase_price}} - {{$sum_sale_price}} = {{number_format($purchase_sale_profit = $sum_purchase_price - $sum_sale_price, 2, '.', '')}}</h6>--}}
{{--                            <h6>Profit after Expense:{{$purchase_sale_profit}} - {{$sum_profit_amount}} = {{number_format($profit_after_expense = $purchase_sale_profit - $sum_profit_amount, 2, '.', '')}}</h6>--}}
{{--                            <h6>Profit after Discount:{{$profit_after_expense}} - {{$sum_total_discount}} = {{ number_format($profit_after_discount = $profit_after_expense - $sum_total_discount, 2, '.', '')}}</h6>--}}
{{--                        </div>--}}

                        <div class="col-md-3 ">
                            <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                                <div class="info">
                                    <h4>Total FG Purchases</h4>
                                    @php
                                        $sum_finish_goods_purchase_price = sum_finish_goods_purchase_price($store->id);
                                    @endphp
                                    <p><b>{{number_format($sum_finish_goods_purchase_price, 2, '.', '')}}</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="widget-small warning coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                                <div class="info">
                                    <h4>Total RM Purchases</h4>
                                    @php
                                        $sum_raw_materials_price = sum_raw_materials_price($store->id);
                                    @endphp
                                    <p><b>{{number_format($sum_raw_materials_price, 2, '.', '')}}</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-small danger coloured-icon"><i class="icon fas fa-money-check-alt "></i>
                                <div class="info">
                                    <h4>Total Sell</h4>
                                    @php
                                        $sum_sale_price = sum_sale_price($store->id);
                                    @endphp
                                    <p><b>{{number_format($sum_sale_price, 2, '.', '')}}</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-small info coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
                                <div class="info">
                                    <h4>Total Sell Return</h4>
                                    @php
                                        $sum_sale_return_price = sum_sale_return_price($store->id);
                                    @endphp
                                    <p><b>{{number_format($sum_sale_return_price, 2, '.', '')}}</b></p>
                                </div>
                            </div>
                        </div>
{{--                        <div class="col-md-3 ">--}}
{{--                            <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>--}}
{{--                                <div class="info">--}}
{{--                                    <h4>Total Production</h4>--}}
{{--                                    <p><b>{{number_format($sum_production_price, 2, '.', '')}}</b></p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-md-3">
                            <div class="widget-small primary coloured-icon"><i class="icon fas fa-file-invoice-dollar"></i>
                                <div class="info">
                                    <h4>Total Expense</h4>
                                    @php
                                        $total_expense = total_expense($store->id,NULL,NULL);
                                    @endphp
                                    <p><b>{{number_format($total_expense, 2, '.', '')}}</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
                                <div class="info">
                                    <h4>Final Loss/Profit</h4>
                                    <p>
                                        <b>
                                            @php
                                                //dd(loss_profit($store->id,NULL,NULL));
                                                    $loss_profit = loss_profit($store->id,NULL,NULL) - $total_expense;
                                            @endphp
                                            @if($loss_profit > 0)
                                                Profit:
                                            @elseif($loss_profit < 0)
                                                Loss:
                                            @else

                                            @endif
                                                {{number_format($loss_profit, 2, '.', '')}}
                                        </b>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a  href="{{ route('transaction.partyDiscount') }}">
                                <div class="widget-small danger coloured-icon"><i class="icon fas fa-money-check-alt "></i>
                                    <div class="info">
                                        <h4>Total Discount</h4>
                                        @php
                                            $product_sale_discount = product_sale_discount($store->id);
                                        @endphp
                                        <p><b>{{number_format($product_sale_discount, 2, '.', '')}}</b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('stock.summary.list') }}">
                                <div class="widget-small info coloured-icon"><i class="icon fas fa-money-check-alt "></i>
                                    <div class="info">
                                        <h4>Stock Summary</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('stock.low.list') }}">
                                <div class="widget-small primary coloured-icon"><i class="icon fas fa-money-check-alt "></i>
                                    <div class="info">
                                        <h4>Stock Low</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 ">
                            <a href="{{ route('productCategories.create') }}">
                                <div class="widget-small warning coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                                    <div class="info">
                                        <h4>Product Category</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('productBrands.create') }}">
                                <div class="widget-small danger coloured-icon"><i class="icon fas fa-money-check-alt "></i>
                                    <div class="info">
                                        <h4>Product Brand</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('productUnits.create') }}">
                                <div class="widget-small info coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
                                    <div class="info">
                                        <h4>Product Unit</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 ">
                            <a href="{{ route('products.create') }}">
                                <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                                    <div class="info">
                                        <h4>Product </h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('party.create') }}">
                                <div class="widget-small warning coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                                    <div class="info">
                                        <h4>Party</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('productPurchases.create') }}">
                                <div class="widget-small danger coloured-icon"><i class="icon fa fa-cart-plus"></i> <div class="info">
                                        <h4>FG Stock In</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
{{--                        <div class="col-md-3">--}}
{{--                            <a href="{{ route('productPosSales.create') }}">--}}
{{--                                <div class="widget-small info coloured-icon"><i class="icon fas fa-file-invoice"></i>--}}
{{--                                    <div class="info">--}}
{{--                                        <h4>POS Sale/Stock Out</h4>--}}
{{--                                        <p><b></b></p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </div>--}}
                        <div class="col-md-3">
                            <a href="{{ route('productSales.create') }}">
                                <div class="widget-small info coloured-icon"> <i class="icon fa fa-sort-amount-asc"></i> <div class="info">
                                        <h4>FG Whole Sale</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a  href="{{ route('returnable.sale.product') }}">
                                <div class="widget-small primary coloured-icon"><i class="icon fa fa-cart-plus"></i> <div class="info">
                                        <h4>FG Sale Return</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('productSaleReplacement.create') }}">
                                <div class="widget-small warning coloured-icon"> <i class="icon fa fa-sort-amount-asc"></i>
                                    <div class="info">
                                        <h4>FG Sale Replace</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a  href="{{ route('productPurchaseRawMaterials.create') }}">
                                <div class="widget-small danger coloured-icon"><i class="icon fa fa-shopping-basket"></i>
                                    <div class="info">
                                        <h4>Raw Materials Stock In</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a  href="{{ route('productProductions.create') }}">
                                <div class="widget-small info coloured-icon"><i class="icon fas fa-file-invoice"></i>
                                    <div class="info">
                                        <h4> Production Raw Materials</h4>
                                        <p><b></b></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
        </div>
        @else
            <h1>
                Only Admin can show At a Glance! User can only Sale permission.
                <a href="{!! route('productSales.create') !!}" class="btn btn-sm btn-primary" type="button">Add Product Sales</a>
            </h1>
        @endif
    </main>
@endsection


@section('footer')

@endsection
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    // Swal.fire({
    //     title: 'Custom animation with Animate.css',
    //     showClass: {
    //         popup: 'animate__animated animate__fadeInDown'
    //     },
    //     hideClass: {
    //         popup: 'animate__animated animate__fadeOutUp'
    //     }
    // })
    //sweet alert
    function deletePost() {
        swal("Here's the title!", "...and here's the text!");
    }
</script>
