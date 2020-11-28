
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
                        /*border-top: 1px solid #000;*/
                        /*border-bottom: 1px solid #000;*/
                        border: 1px solid #000;
                    }

                    .invoice td {
                        text-align: center;
                        font-size: 18px;
                        border: 1px solid #000;
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
                        margin: 18px 50px !important;
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
                    <div class="row" style="border-bottom: 1px solid #000000;">
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
                            <strong>To,</strong><br>
                            <strong>{{$party->name}}</strong><br>
                            {{$party->address}}
                        </div>
                        <div class="col-md-6" style="text-align: right; width: 40%; display: inline-block">
                            <div class="invoice-to">
                                <table>
                                    <tr>
                                        <th style="text-align: left;font-size: 18px;border-right: 1px solid #000000">Invoice</th>
                                        <th style="text-align: left;font-size: 18px;">#{{$productSale->invoice_no}}</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;border-right: 1px solid #000000"">Date:</td>
                                        <td style="text-align: left;font-size: 18px;">{{date('d-m-Y')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;border-right: 1px solid #000000"">Phone NO:</td>
                                        <td style="text-align: left;font-size: 18px;">{{$party->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 18px;border-right: 1px solid #000000"">Creditor BY:</td>
                                        <td style="text-align: left;font-size: 18px;">{{\Illuminate\Support\Facades\Auth::user()->name}}</td>
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
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>Subtotal:</td>
                            <td>{{$sum_sub_total}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>Discount:</td>
                            <td>-{{$productSale->discount_amount}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>Total Amount</td>
                            <td>{{$productSale->total_amount}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>Paid Amount:</td>
                            <td>{{$productSale->paid_amount}}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td>Due Amount:</td>
                            <td>{{$productSale->due_amount}}</td>
                        </tr>
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
                        <div class="row">
                            <div class="col-md-6" style="width: 60%; float: left;display: inline-block">
                                <strong>Received By</strong><br>
                                <strong>Customer signature</strong>
                            </div>
                            <div class="col-md-6" style="text-align: right; width: 40%; display: inline-block">
                                <strong>Authorize Signature</strong><br>
                                <strong>For SIMCO Electronics</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row footer_div">
                        <div style="width: 20%;float: left;display: inline-block">
                            <strong>SIMCO Electronics</strong> <br>
                            Square Tower, 3-B Level-4
                            36/6, Mirpur Road
                            Bashundhara Lane
                            Dhaka-1205, Bangladesh.<br>
                        </div>
                        <div style="width: 20%;float: left;display: inline-block">
                            Phone: +88-02-9662755<br>
                            Cell: +88-01711-530918<br>
                            +88-01971-530918<br>
                            Fax: +88-02-58616169
                        </div>
                        <div style="width: 20%;float: left;display: inline-block">
                            simcodhaka<br>@gmail.com<br>
                            simco91<br>@gmail.com<br>
                            www.simco<br>.com.bd<br>
                        </div>
                        <div style="width: 20%;float: left;display: inline-block">
                            Prime Bank Ltd.
                            BRAC Bank Ltd.
                            NCC Bank Ltd.
                            Trust Bank Ltd.
                            Agrani Bank
                            Ltd.
                        </div>
                        <div style="width: 20%;float: left;display: inline-block">
                            CD A/C #:
                            02114117001874
                            CD A/C #:
                            1524204051833001
                            CD A/C #:
                            00430210000068
                            CD A/C #:
                            00530210005141
                            CD A/C #: 0200010401754
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


