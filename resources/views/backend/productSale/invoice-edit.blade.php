@extends('backend._partial.dashboard')
<style>
    .requiredCustom{
        font-size: 20px;
        color: red;
        margin-top: 20px;
    }
</style>
@section('content')
{{--    <link rel="stylesheet" href="{{asset('backend/plugins/fontawesome-free/css/all.min.css')}}">--}}
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
                                <div class="row">
                                    <div class="col-12">
                                        <h4>
                                            <img src="{{asset('uploads/store/'.$store->logo)}}" alt="logo" height="60px" width="250px">
                                            <small class="float-right">Date: {{date('d-m-Y')}}</small>
                                        </h4>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        From
                                        <address>
                                            <strong>{{$store->name}}</strong><br>
                                            {{$store->address}}<br>
                                            Phone: {{$store->phone}}<br>
                                            Email:
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        To
                                        <address>
                                            <strong>{{$party->name}}</strong><br>
                                            {{$party->address}}<br>
                                            Phone: {{$party->phone}}<br>
                                            Email: {{$party->email}}
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        <b>Invoice #{{$productSale->invoice_no}}</b><br>
                                        <br>
{{--                                        <b>Order ID:</b> 4F3S8J<br>--}}
{{--                                        <b>Payment Type:</b> {{$productSale->payment_type}}<br>--}}
                                        <b>Delivery Service:</b> {{$productSale->delivery_service}}
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
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">
                                    <!-- accepted payments column -->
                                    <div class="col-6">
{{--                                        <p class="lead">Payment Methods:</p>--}}
{{--                                        <img src="{{asset('backend/dist/img/credit/visa.png')}}" alt="Visa">--}}
{{--                                        <img src="{{asset('backend/dist/img/credit/mastercard.png')}}" alt="Mastercard">--}}
{{--                                        <img src="{{asset('backend/dist/img/credit/american-express.png')}}" alt="American Express">--}}
{{--                                        <img src="{{asset('backend/dist/img/credit/paypal2.png')}}" alt="Paypal">--}}

{{--                                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">--}}
{{--                                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem--}}
{{--                                            plugg--}}
{{--                                            dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.--}}
{{--                                        </p>--}}
                                        <p class="lead">Payment Type:</p>
                                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                            {{$productSale->payment_type}}
                                        </p>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-6">
{{--                                        <p class="lead">Amount Due 2/22/2014</p>--}}
                                        <p class="lead">Amount</p>

                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th style="width:50%">Subtotal:</th>
                                                    <td>
                                                        {{$sum_sub_total}}
                                                    </td>
                                                </tr>
{{--                                                <tr>--}}
{{--                                                    <th>Tax (9.3%)</th>--}}
{{--                                                    <td>$10.34</td>--}}
{{--                                                </tr>--}}
                                                <tr>
                                                    <th>Discount:</th>
                                                    <td>{{$productSale->discount_amount}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Amount:</th>
                                                    <td>{{$productSale->total_amount}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Paid Amount:</th>
                                                    <td>{{$productSale->paid_amount}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Due Amount:</th>
                                                    <td>{{$productSale->due_amount}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Previous Due Amount:</th>
                                                    <td>
                                                        @php
                                                            $product_sale_dues = \App\ProductSale::query()
                                                            ->select(DB::raw('SUM(due_amount) as due_amount'))
                                                            //->where('id','!=',$productSale->id)
                                                            ->where('id','<',$productSale->id)
                                                            //->groupBy('product_id')
                                                            ->first();

                                                            //$sum_previous_due_amount = $product_sale_dues->due_amount;

                                                        //dd($product_sale_dues->due_amount);
                                                        $previous_due_amount = $product_sale_dues->due_amount;
                                                        if(!empty($previous_due_amount)){
                                                            echo $previous_due_amount;
                                                        }else{
                                                            echo $previous_due_amount = 0;
                                                        }
                                                        @endphp
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Final Due Amount:</th>
                                                    <td>{{$productSale->due_amount+$previous_due_amount}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div class="col-12">
                                        <a href="{{route('productSales-invoice-print',$productSale->id)}}" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
{{--                                        <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit--}}
{{--                                            Payment--}}
{{--                                        </button>--}}
{{--                                        <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">--}}
{{--                                            <i class="fas fa-download"></i> Generate PDF--}}
{{--                                        </button>--}}
                                    </div>
                                </div>


                                <h1 class="requiredCustom">
                                    <span>* Outside product first add for syn stock and loss/profit management.</span>
                                    <a href="{!! route('productPurchases.create') !!}" class="btn btn-sm btn-primary" type="button">Add Product Purchases</a>
                                </h1>
                                <input type="button" class="btn btn-primary add " style="float: right" value="Add More Sale Product">
                                <form method="post" action="{{ route('productSales.invoiceUpdate',$productSale->id) }}">
{{--                                    @method('PUT')--}}
                                    @csrf

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th >ID</th>
                                        <th>Product <small class="requiredCustom">*</small></th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Brand</th>
                                        <th>Return</th>
                                        <th>Stock Qty</th>
                                        <th>Qty <small class="requiredCustom">*</small></th>
                                        <th>Price <small class="requiredCustom">*</small></th>
                                        <th>Sub Total</th>
                                        <th>Action</th>

                                    </tr>
                                    </thead>
                                    <tbody class="neworderbody">
                                    <tr>
                                        <td width="5%" class="no">1</td>
                                        <td>
                                            <input type="hidden" name="store_id" id="store_id" value="{{$store->id}}">
                                            <select class="form-control product_id select2" name="product_id[]" id="product_id_1" onchange="getval(1,this);" required>
                                                <option value="">Select  Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div id="product_category_id_1">
                                                <select class="form-control product_category_id select2" name="product_category_id[]"  required>
                                                    <option value="">Select  Category</option>
                                                    @foreach($productCategories as $productCategory)
                                                        <option value="{{$productCategory->id}}">{{$productCategory->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="product_sub_category_id_1">
                                                <select class="form-control product_sub_category_id select2" name="product_sub_category_id[]">
                                                    <option value="">Select  Sub Category</option>
                                                    @foreach($productSubCategories as $productSubCategory)
                                                        <option value="{{$productSubCategory->id}}">{{$productSubCategory->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="product_brand_id_1">
                                                <select class="form-control product_brand_id select2" name="product_brand_id[]" required>
                                                    <option value="">Select  Brand</option>
                                                    @foreach($productBrands as $productBrand)
                                                        <option value="{{$productBrand->id}}">{{$productBrand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="return_type[]" id="return_type_id_1" class="form-control" >
                                                <option value="returnable" selected>returnable</option>
                                                <option value="not returnable">not returnable</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" id="stock_qty_1" class="stock_qty form-control" name="stock_qty[]" value="" readonly >
                                        </td>
                                        <td>
                                            <input type="number" min="1" max="" class="qty form-control" name="qty[]" value="" required >
                                        </td>
                                        <td>
                                            <input type="number" min="1" max="" class="price form-control" name="price[]" value="" required >
                                        </td>
                                        <td>
                                            <input type="text" class="amount form-control" name="sub_total[]">
                                        </td>
                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th colspan="2">
                                            Discount Type:
                                            <select name="discount_type" id="discount_type" class="form-control" >
                                                <option value="flat" {{$productSale->discount_type == 'flat' ? 'selected' : ''}}>flat</option>
                                                <option value="percentage" {{$productSale->discount_type == 'percentage' ? 'selected' : ''}}>percentage</option>
                                            </select>
                                        </th>
                                        <th colspan="2">
                                            Discount Amount:
                                            <input type="text" id="discount_amount" class="form-control" name="discount_amount" value="{{$productSale->discount_amount}}">
                                        </th>
                                        <th colspan="2">
                                            Total:
                                            <input type="hidden" name="previous_sum_sub_total" id="previous_sum_sub_total" value="{{$sum_sub_total}}">
                                            <input type="text" id="total_amount" class="form-control" name="current_total_amount" value="{{$productSale->total_amount}}">
                                        </th>
                                        <th colspan="2">
                                            Paid Amount:
                                            <input type="text" id="paid_amount" class="getmoney form-control" name="paid_amount" value="{{$productSale->paid_amount}}">
                                        </th>
                                        <th colspan="2">
                                            Due Amount:
                                            <input type="text" id="due_amount" class="backmoney form-control" name="due_amount" value="{{$productSale->due_amount}}">
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="9">&nbsp;</td>
                                        <td>
                                            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update Invoice</button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                                </form>
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

@push('js')
    <script>

        function totalAmount(){
            var t = 0;
            $('.amount').each(function(i,e){
                var amt = $(this).val()-0;
                t += amt;
            });
            var previous_sum_sub_total = $("#previous_sum_sub_total").val();
            t += parseInt(previous_sum_sub_total);
            $('#total_amount').val(t);
            //$('#paid_amount').val(0);
            //$('#due_amount').val(0);
        }
        $(function () {
            $('#discount_amount').change(function(){
                var discount_type = $('#discount_type').val();
                var total = $('#total_amount').val();
                var getmoney = $(this).val();
                if(discount_type == 'flat'){
                    var t = total - getmoney;
                }
                else{
                    var per = (total*getmoney)/100;
                    var t = total-per;
                }
                $('#total_amount').val(t);
            });
            $('.getmoney').change(function(){
                var total = $('#total_amount').val();
                var getmoney = $(this).val();
                var t = total - getmoney;
                $('.backmoney').val(t);
            });
            $('.add').click(function () {
                var productCategory = $('.product_category_id').html();
                var productSubCategory = $('.product_sub_category_id').html();
                var productBrand = $('.product_brand_id').html();
                var product = $('.product_id').html();
                var n = ($('.neworderbody tr').length - 0) + 1;
                var tr = '<tr><td class="no">' + n + '</td>' +
                    '<td><select class="form-control product_id select2" name="product_id[]" id="product_id_'+n+'" onchange="getval('+n+',this);" required>' + product + '</select></td>' +
                    '<td><div id="product_category_id_'+n+'"><select class="form-control product_category_id select2" name="product_category_id[]" required>' + productCategory + '</select></div></td>' +
                    '<td><div id="product_sub_category_id_'+n+'"><select class="form-control product_sub_category_id select2" name="product_sub_category_id[]" required>' + productSubCategory + '</select></div></td>' +
                    '<td><div id="product_brand_id_'+n+'"><select class="form-control product_brand_id select2" name="product_brand_id[]" id="product_brand_id_'+n+'" required>' + productBrand + '</select></div></td>' +
                    '<td><select name="return_type[]" id="return_type_id_'+n+'" class="form-control" ><option value="returnable" selected>returnable</option><option value="not returnable">not returnable</option></select></td>' +
                    '<td><input type="number" id="stock_qty_'+n+'" class="stock_qty form-control" name="stock_qty[]" readonly></td>' +
                    '<td><input type="number" min="1" max="" class="qty form-control" name="qty[]" required></td>' +
                    '<td><input type="text" min="1" max="" class="price form-control" name="price[]" value="" required></td>' +
                    //'<td><input type="number" min="0" value="0" max="100" class="dis form-control" name="discount[]" required></td>' +
                    '<td><input type="text" class="amount form-control" name="sub_total[]" required></td>' +
                    '<td><input type="button" class="btn btn-danger delete" value="x"></td></tr>';

                $('.neworderbody').append(tr);

                //initSelect2();

                $('.select2').select2();

            });
            $('.neworderbody').delegate('.delete', 'click', function () {
                $(this).parent().parent().remove();
                totalAmount();
            });

            $('.neworderbody').delegate('.qty, .price', 'keyup', function () {
                var gr_tot = 0;
                var tr = $(this).parent().parent();
                var qty = tr.find('.qty').val() - 0;
                var stock_qty = tr.find('.stock_qty').val() - 0;
                if(qty > stock_qty){
                    alert('You have limit cross of stock qty!');
                    tr.find('.qty').val(0)
                }

                //var dis = tr.find('.dis').val() - 0;
                var price = tr.find('.price').val() - 0;

                //var total = (qty * price) - ((qty * price)/100);
                //var total = (qty * price) - ((qty * price * dis)/100);
                //var total = price - ((price * dis)/100);
                //var total = price - dis;
                var total = (qty * price);

                tr.find('.amount').val(total);
                //Total Price
                $(".amount").each(function() {
                    isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
                });
                //var final_total = gr_tot;
                var previous_sum_sub_total = $("#previous_sum_sub_total").val();
                var discount = parseInt($("#discount_amount").val());
                var final_total     = (parseInt(previous_sum_sub_total) + parseInt(gr_tot)) - discount;
                console.log(typeof final_total);
                //$("#total_amount").val(final_total.toFixed(2,2));
                $("#total_amount").val(final_total);
                var t = $("#total_amount").val(),
                    a = $("#paid_amount").val(),
                    e = t - a;
                //$("#remaining_amnt").val(e.toFixed(2,2));
                $("#due_amount").val(e);
                totalAmount();
            });

            $('#hideshow').on('click', function(event) {
                $('#content').removeClass('hidden');
                $('#content').addClass('show');
                $('#content').toggle('show');
            });



        });


        // ajax
        function getval(row,sel)
        {
            var store_id = $('#store_id').val();
            if(store_id){
                //console.log(store_id)
                //alert(row);
                //alert(sel.value);
                var current_row = row;
                var current_product_id = sel.value;

                $.ajax({
                    url : "{{URL('product-sale-relation-data')}}",
                    method : "get",
                    data : {
                        store_id : store_id,
                        current_product_id : current_product_id,
                    },
                    success : function (res){
                        //console.log(res)
                        console.log(res.data)
                        //console.log(res.data.categoryOptions)
                        $("#product_category_id_"+current_row).html(res.data.categoryOptions);
                        $("#product_sub_category_id_"+current_row).html(res.data.subCategoryOptions);
                        $("#product_brand_id_"+current_row).html(res.data.brandOptions);
                        $("#stock_qty_"+current_row).val(res.data.current_stock);
                    },
                    error : function (err){
                        console.log(err)
                    }
                })
            }else{
                alert('Please select first store!');
                location.reload();
            }
        }





        function modal_customer(){
            $('#customar_modal').modal('show');
        }

        //new customer insert
        $("#customer_insert").submit(function(e){
            e.preventDefault();
            //var customerMess    = $("#customerMess3");
            //var customerErrr    = $("#customerErrr3");
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                dataType: 'json',
                data: $(this).serialize(),
                beforeSend: function()
                {
                    //customerMess.removeClass('hide');
                    //customerErrr.removeClass('hide');
                },
                success: function(data)
                {
                    console.log(data);
                    if (data.exception) {
                        customerErrr.addClass('alert-danger').removeClass('alert-success').html(data.exception);
                    }else{
                        $('#customer').append('<option value = "' + data.id + '"  selected> '+ data.name + ' </option>');
                        console.log(data.id);
                        $("#customar_modal").modal('hide');
                    }
                },
                error: function(xhr)
                {
                    alert('failed!');
                }
            });
        });

        function hidemodal() {
            var x = document.getElementById("customar_modal");
            x.style.display = "none";
        }
    </script>
@endpush


