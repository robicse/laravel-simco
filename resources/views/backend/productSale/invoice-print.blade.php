
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

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
                    /* Styles go here */

                    .page-header, .page-header-space {
                        height: 50px;
                    }

                    .page-footer, .page-footer-space {
                        height: 70px !important;
                    }

                    .page-header {
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        width: 100%;
                    }

                    .page-footer {
                        border-top: 1px solid #000000;
                        font-size: 10px !important;
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        width: 100%;
                    }

                    /*custom part start*/

                    .panel-body {
                        min-height: 1000px !important;
                        font-size: 16px !important;
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        font-weight: inherit;
                    }

                    .invoice {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    .invoice th, .invoice-to {
                        border: 1px solid #000;
                    }

                    .invoice td {
                        text-align: center;
                        font-size: 16px;
                        border: 1px solid #000;
                    }

                    td.container_div {
                        height: 90px;
                        width: 100%;
                    }
                    span.left_signature strong {
                        float: left !important;
                        padding-top: 120px !important;
                        margin-left: 100px !important;
                    }

                    .page {
                        page-break-after: always;
                    }
                    @page {
                        size: A4;
                    }
                </style>

                <div class="panel-body">
                    <div class="page-header" style="text-align: center">
                        <div class="row" style="padding-top: 5px; margin-right: 10px;">
                            <div class="col-md-12" style="text-align: right; width: 100%; display: inline-block">
                                <div class="invoice-logo">
                                    <img src="{{asset('uploads/store/'.$store->logo)}}" alt="logo" height="60px" width="200px">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-footer">
                        <div style="width: 21%;float: left;display: inline-block">
                            <strong>SIMCO Electronics</strong> <br>
                            Square Tower, 3-B Level-4
                            36/6, Mirpur Road
                            Bashundhara Lane
                            New Market
                            Dhaka-1205, Bangladesh.
                        </div>
                        <div style="width: 21%;float: left;display: inline-block">
                            <div style="width: 25%;float: left;">Phone:</div>
                            <div style="width: 75%;float: left;">+88-02-223362755</div>
                            <div style="width: 25%;float: left;">Cell:</div>
                            <div style="width: 75%;float: left;">+88-01711-530918</div>
                            <div style="width: 25%;float: left;">&nbsp;</div>
                            <div style="width: 75%;float: left;">+88-01971-530918</div>
                            <div style="width: 25%;float: left;">Fax:</div>
                            <div style="width: 75%;float: left;">+88-02-58616169</div>
                        </div>
                        <div style="width: 20%;float: left;display: inline-block">
                            simcodhaka@gmail.com
                            simco91@gmail.com
                            info@simco.com.bd
                            www.simco.com.bd
                        </div>
                        <div style="width: 38%;float: left;display: inline-block">
                            <div style="width: 39%;float: left;">Prime Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 02114117001874</div>
                            <div style="width: 39%;float: left;">BRAC Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 1524204051833001</div>
                            <div style="width: 39%;float: left;">NCC Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 00430210000068</div>
                            <div style="width: 39%;float: left;">Trust Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 00530210005141</div>
                            <div style="width: 39%;float: left;">Agrani Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 0200010401754</div>
                            <div style="width: 39%;float: left;">Union Bank Ltd.</div>
                            <div style="width: 61%;float: left;">CD A/C #: 0941010000128</div>
                        </div>
                    </div>

                    <table>

                        <thead>
                        <tr>
                            <td>
                                <!--place holder for the fixed-position header-->
                                <div class="page-header-space"></div>
                            </td>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>
                                <!--*** CONTENT GOES HERE ***-->
                                <div class="page" style="">
                                    <div class="row">
                                        <div class="col-md-6" style="width: 60%; float: left;display: inline-block">
                                            <strong>To,</strong><br>
                                            <strong>{{$party->name}}</strong><br>
                                            Address:{{$party->address}}<br>
                                            @if($party->phone)
                                            Mobile: {{$party->phone}}<br>
                                            @endif
                                            ID NO: {{$party->id}}<br>
                                        </div>
                                        <div class="col-md-6" style="text-align: right; width: 40%; display: inline-block">
                                            <div class="invoice-to">
                                                <table>
                                                    <tr>
                                                        <td style="text-align: left;font-size: 16px;border-right: 1px solid #000000">Invoice No.</td>
                                                        <td style="text-align: left;font-size: 16px;">{{$productSale->invoice_no}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;font-size: 16px;border-right: 1px solid #000000">DateTime:</td>
                                                        <td style="text-align: left;font-size: 16px;">{{$productSale->created_at}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;font-size: 16px;border-right: 1px solid #000000">BIN Reg. NO:</td>
                                                        <td style="text-align: left;font-size: 16px;">001719214-0201</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;font-size: 16px;border-right: 1px solid #000000">Phone No.</td>
                                                        <td style="text-align: left;font-size: 16px;">02-223362755</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: left;font-size: 16px;border-right: 1px solid #000000">Creditor BY:</td>
                                                        <td style="text-align: left;font-size: 16px;">{{\Illuminate\Support\Facades\Auth::user()->name}}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <br/>
                                    <h1 style="text-align: center"><strong>Bill</strong></h1>
                                    <table class="invoice">
                                        <thead>
                                        <tr style="background-color: #dddddd">
                                            <th>SL No.</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Unit Price BDT</th>
                                            <th>Amount BDT</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $sum_sub_total = 0;
                                        @endphp
                                        @foreach($productSaleDetails as $key => $productSaleDetail)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td style="text-align: left">{{$productSaleDetail->product->name}}</td>
                                                <td>{{$productSaleDetail->qty}}</td>
                                                <td>{{$productSaleDetail->product_unit->name}}</td>
                                                <td style="text-align: right">{{number_format($productSaleDetail->price,2)}}</td>
                                                <td style="text-align: right">
                                                    @php
                                                        $sub_total=$productSaleDetail->qty*$productSaleDetail->price;
                                                        $sum_sub_total += $sub_total;
                                                    @endphp
                                                    {{number_format($sub_total,2)}}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="4">&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Subtotal:</th>
                                            <th style="text-align: right">{{number_format($sum_sub_total, 2)}}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Discount:</th>
                                            <th style="text-align: right">
                                                -{{number_format($productSale->discount_amount,2)}}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Total Amount:</th>
                                            <th style="text-align: right">{{number_format($productSale->total_amount,2)}}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Paid Amount:</th>
                                            <th style="text-align: right">{{number_format($productSale->paid_amount,2)}}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Due Amount:</th>
                                            <th style="text-align: right">{{number_format($productSale->due_amount,2)}}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">&nbsp;</th>
                                            <th style="text-align: right">Previous Due Amount:</th>
                                            <th style="text-align: right">{{number_format($previous_due,2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="write">
                                        <p class="lead"><b>In Word : {{ucwords($digit->format($productSale->total_amount))}} Only.</b></p>
                                    </div>
                                    <div class="row" style="">
                                        <!-- accepted payments column -->
                                        <div class="col-md-6">

                                            <p style="text-align: left;font-size: 16px;" class="lead">Payment Type:</p>
                                            <p style="text-align: left;font-size: 16px;" class="text-muted well well-sm shadow-none" >
                                            @if(!empty($transactions))
                                                <ul>
                                                    @foreach($transactions as $transaction)
                                                        <li>
                                                            {{$transaction->payment_type}}
                                                            @if($transaction->payment_type == 'Cheque')
                                                                ( Cheque Number: {{$transaction->cheque_number}} )
                                                            @endif
                                                            :
                                                            Tk.{{number_format($transaction->amount,2)}} ({{$transaction->created_at}})
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                                </p>
                                        </div>
                                        <!-- /.col -->
                                    </div>

                                    <div class="row" style="margin-top: 10%">
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
                            </td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td>
                                <!--place holder for the fixed-position footer-->
                                <div class="page-footer-space"></div>
                            </td>
                        </tr>
                        </tfoot>

                    </table>
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


