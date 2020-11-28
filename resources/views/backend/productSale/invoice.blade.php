@extends('backend._partial.dashboard')
<style>
.invoice-to{
    /*width: 401px;*/
    padding: 10px;
    border: 2px solid black;
    margin: 0;
}

</style>
@section('content')
    <link rel="stylesheet" href="{{asset('backend/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('backend/dist/css/adminlte.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Invoice</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Invoice</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="callout callout-info">
                                <h5><i class="fas fa-info"></i> Note:</h5>
                                This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
                            </div>


                            <!-- Main content -->
                            <div class="invoice p-3 mb-3">
                                <!-- title row -->
                                <div class="row" style="border-bottom: 1px solid #000000;">
                                    <div class="col-12">
                                        <h4 style="float: right">
                                            <img class="float-right" src="{{asset('uploads/store/'.$store->logo)}}" alt="logo" height="60px" width="200px"><br><br>
                                            <small class="float-right"> {{date('d-m-Y')}}</small><br>
                                            <small class="float-right">Invoice #{{$productSale->invoice_no}}</small><br>
                                        </h4>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-md-8 invoice-col">
                                        <address>
                                            <strong>To,</strong><br>
                                            <strong>{{$party->name}}</strong><br>
                                            {{$party->address}}
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-4 invoice-col">
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
                                    <!-- /.col -->

                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
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
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">
                                    <!-- accepted payments column -->
                                    <div class="col-6">
                                        <p class="lead">Payment Type:</p>
                                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
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
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
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
                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div class="col-12">
                                        <a href="{{route('productSales-invoice-print',$productSale->id)}}" target="_blank" class="btn btn-success float-right"><i class="fas fa-print"></i> Print</a>
{{--                                        <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit--}}
{{--                                            Payment--}}
{{--                                        </button>--}}
{{--                                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">--}}
{{--                                            <i class="fas fa-download"></i> Generate PDF--}}
{{--                                        </button>--}}
                                    </div>
                                </div>
                            </div>
                            <!-- /.invoice -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('backend/dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('backend/dist/js/demo.js')}}"></script>

@endsection


