
<!-- Google Font: Source Sans Pro -->
{{--<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">--}}

<!-- Printable area end -->
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4></h4>
                </div>
            </div>
            <div id="printArea">
                <style>
                    .panel-body {
                        min-height: 1000px !important;
                        font-size: 18px !important;
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        font-weight: inherit;
                    }
                    .invoice {
                        border-collapse: collapse;
                        width: 100%;
                    }

                    .invoice th {
                        border-top: 1px solid #000;
                        border-bottom: 1px solid #000;
                    }

                    .invoice td {
                        text-align: center;
                        font-size: 18px;
                    }

                    .invoice-logo{
                        margin-right: 0;
                    }

                    .invoice-logo > img, .invoice-logo > span {
                        float: right !important;
                    }

                    .invoice-to{
                        border: 1px solid black;
                        margin: 0;
                    }

                    .footer_div {
                        position:absolute;
                        bottom: 0 !important;
                        border-top: 1px solid #000000;
                        width:100%;
                    }

                    /* default settings */
                    /*.page {*/
                    /*    page-break-after: always;*/
                    /*}*/

                    @page {
                        size: A4;
                        /*size: Letter;*/
                        /*margin: 0px !important;*/
                        margin: 18px 100px !important;
                    }

                    /*@media screen {*/
                    /*    .page-header {display: none;}*/
                    /*    .page-footer {display: none;}*/

                    /*}*/

                    /*@media print {*/
                    /*    table { page-break-inside:auto }*/
                    /*    tr    { page-break-inside:auto; page-break-after:auto }*/
                    /*    thead { display:table-header-group }*/
                    /*    tfoot { display:table-footer-group }*/
                    /*    button {display: none;}*/
                    /*    body {margin: 0;}*/
                    /*}*/
                    /* default settings */

                </style>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6" style="width: 80%; float: left;display: inline-block">&nbsp;</div>
                        <div class="col-md-6" style="text-align: right; width: 20%; display: inline-block">
                            <div class="invoice-logo">
                                <img src="{{asset('uploads/store/'.$store->logo)}}" alt="logo" height="60px" width="200px">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="width: 100%; float: left;display: inline-block">&nbsp;</div>
                        <div class="col-md-6" style="text-align: right; width: 100%; display: inline-block">
                            <div class="invoice-logo">
                                <span style="font-size: 18px;"> {{date('d-m-Y')}}</span><br>
                                <small class="float-right" style="font-size: 18px;">Invoice #{{$productSale->invoice_no}}</small><br>
                            </div>
                        </div>
                    </div>
                    <div>&nbsp;</div>
                    <div class="row">
                        <div class="col-md-6" style="width: 60%; float: left;display: inline-block">
                            <strong>Simco Main Store</strong><br>
                            Flat # 3-B, (3rd floor)<br>
                            Square Tower (Bashundhara Lane)<br>
                            36/6, Mirpur Road <br><br>
                            Dhaka-1205<br>
                            Bangladesh
                        </div>
                        <div class="col-md-6" style="text-align: right; width: 40%; display: inline-block">
                            <div class="invoice-to">
                                <table>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Invoice</th>
                                        <th style="text-align: left;font-size: 18px;">#{{$productSale->invoice_no}}</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;">Customer Name:</td>
                                        <td style="text-align: left;font-size: 18px;">{{$party->name}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;">Phone NO:</td>
                                        <td style="text-align: left;font-size: 18px;">{{$party->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;">Email:</td>
                                        <td style="text-align: left;font-size: 18px;">{{$party->email}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;">Creditor:</td>
                                        <td style="text-align: left;font-size: 18px;">Test</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <table class="invoice">
                        <thead>
                        <tr>
                            <th>SL#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sum_sub_total = 0;
                        @endphp
                        @foreach($productSaleDetails as $key => $productSaleDetail)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$productSaleDetail->product->name}}</td>
                                <td>{{$productSaleDetail->qty}}</td>
                                <td>{{$productSaleDetail->price}}</td>
                                <td>
                                    @php
                                        $sub_total=$productSaleDetail->qty*$productSaleDetail->price;
                                        $sum_sub_total += $sub_total;
                                    @endphp
                                    {{$sub_total}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row" style="">
                        <!-- accepted payments column -->
                        <div class="col-md-6">

                            <p style="text-align: left;font-size: 18px;" class="lead">Payment Type:</p>
                            <p style="text-align: left;font-size: 18px;" class="text-muted well well-sm shadow-none" >
                                {{$transaction->payment_type}}
                                @if($transaction->payment_type == 'check')
                                    ( Check Number: {{$transaction->check_number}} )
                                @endif
                            </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6" style="float: right;margin-top: -100px">
                            {{--                                        <p class="lead">Amount Due 2/22/2014</p>--}}
                            <p class="lead">Amount</p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Subtotal:</th>
                                        <td style="text-align: left;font-size: 18px;">{{$sum_sub_total}}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Discount:</th>
                                        <td style="text-align: left;font-size: 18px;">{{$productSale->discount_amount}}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Total Amount:</th>
                                        <td style="text-align: left;font-size: 18px;">{{$productSale->total_amount}}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Paid Amount:</th>
                                        <td style="text-align: left;font-size: 18px;">{{$productSale->paid_amount}}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;">Due Amount:</th>
                                        <td style="text-align: left;font-size: 18px;">{{$productSale->due_amount}}</td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row footer_div">
                        <div class="col-md-6" style="width: 50%;float: left;display: inline-block">
                            <strong>Mr. ASM Ibrahim</strong> <br>
                            Director of Simco<br>
                        </div>
                        <div class="col-md-6" style="width: 50%;float: left;display: inline-block">
                            Mob : +88-02-9662755<br>
                            IP : +88-02-8624637
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>

<script type="text/javascript">
    window.addEventListener("load", window.print());
</script>


