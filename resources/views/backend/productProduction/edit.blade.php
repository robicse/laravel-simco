@extends('backend._partial.dashboard')
<style>
    .requiredCustom{
        font-size: 20px;
        color: red;
        margin-top: 20px;
    }
</style>
@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> Edit Production Product</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('productProductions.index') }}" class="btn btn-sm btn-primary col-sm" type="button">All Production Product</a>
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Edit Production Product</h3>
                <div class="tile-body tile-footer">
                    @if(session('response'))
                        <div class="alert alert-success">
                            {{ session('response') }}
                        </div>
                    @endif
                    <form method="post" action="{{ route('productProductions.update',$productProduction->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group row" @if(Auth::user()->roles[0]->name == 'User') style="display: none" @endif>
                            <label class="control-label col-md-3 text-right">Store  <small class="requiredCustom">*</small></label>
                            <div class="col-md-8">
                                <select name="store_id" id="store_id" class="form-control" >
{{--                                    <option value="">Select One</option>--}}
                                    @foreach($stores as $store)
                                        <option value="{{$store->id}}" {{$store->id == $productProduction->store_id ? 'selected' : ''}}>{{$store->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" style="display: none">
                            <label class="control-label col-md-3 text-right">Payment Type  <small class="requiredCustom">*</small></label>
                            <div class="col-md-8">
                                <select name="payment_type" id="payment_type" class="form-control" required>
                                    <option value="">Select One</option>
                                    <option value="cash" selected>cash</option>
                                    <option value="check">check</option>
                                </select>
                                <span>&nbsp;</span>
                                <input type="text" name="check_number" id="check_number" class="form-control" placeholder="Check Number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Date <small class="requiredCustom">*</small></label>
                            <div class="col-md-8">
                                <input type="text" name="date" class="datepicker form-control" value="{{$productProduction->date}}">
                            </div>
                        </div>

                        <input type="button" class="btn btn-primary add " style="margin-left: 804px;" value="Add More Product">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th >ID</th>
                                <th>Product <small class="requiredCustom">*</small></th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Brand</th>
                                <th>Stock Qty</th>
                                <th>Qty <small class="requiredCustom">*</small></th>
                                <th>Production</th>
                                <th>Price <small class="requiredCustom">*</small></th>
                                <th>Sub Total</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody class="neworderbody">
                            @foreach($productProductionDetails as $key => $productProductionDetail)
                            <tr>
                                @php
                                    $current_row = $key+1;
                                @endphp
                                <td width="5%" class="no">{{$current_row}}</td>
                                <td>
                                    <input type="hidden" class="form-control" name="product_production_detail_id[]" value="{{$productProductionDetail->id}}" >
                                    <select class="form-control product_id select2" name="product_id[]" id="product_id_1" onchange="getval(1,this);" required>
                                        <option value="">Select  Product</option>
                                        @foreach($products as $product)
                                            <option value="{{$product->id}}" {{$product->id == $productProductionDetail->product_id ? 'selected' : ''}}>{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div id="product_category_id_1">
                                        <select class="form-control product_category_id select2" name="product_category_id[]"  required>
                                            <option value="">Select  Category</option>
                                            @foreach($productCategories as $productCategory)
                                                <option value="{{$productCategory->id}}" {{$productCategory->id == $productProductionDetail->product_category_id ? 'selected' : ''}}>{{$productCategory->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div id="product_sub_category_id_1">
                                        <select class="form-control product_sub_category_id select2" name="product_sub_category_id[]">
                                            <option value="">Select  Sub Category</option>
                                            @foreach($productSubCategories as $productSubCategory)
                                                <option value="{{$productSubCategory->id}}" {{$productSubCategory->id == $productProductionDetail->product_sub_category_id ? 'selected' : ''}}>{{$productSubCategory->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div id="product_brand_id_1">
                                        <select class="form-control product_brand_id select2" name="product_brand_id[]" required>
                                            <option value="">Select  Brand</option>
                                            @foreach($productBrands as $productBrand)
                                                <option value="{{$productBrand->id}}" {{$productBrand->id == $productProductionDetail->product_brand_id ? 'selected' : ''}}>{{$productBrand->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" id="stock_qty_1" class="stock_qty form-control" name="stock_qty[]" value="" readonly >
                                </td>
                                <td>
                                    <input type="number" min="1" max="" class="qty form-control" name="qty[]"  value="{{$productProductionDetail->qty}}" required >
                                </td>
                                <td>
                                    <input type="text" min="1" max="" class="production form-control" name="production[]" value="{{$productProductionDetail->production}}" required >
                                </td>
                                <td>
                                    <input type="number" id="price_1" min="1" max="" class="price form-control" name="price[]"  value="{{$productProductionDetail->qty}}" required >
                                </td>
                                <td>
                                    <input type="text" class="amount form-control" name="sub_total[]" value="{{$productProductionDetail->sub_total}}">
                                </td>
                            </tr>
                            @endforeach
                            </tbody>

                        </table>
                        <div class="form-group row">
                            <label class="control-label col-md-3"></label>
                            <div class="col-md-8">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save Product Production</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tile-footer">
                </div>
            </div>
        </div>

@endsection

@push('js')
    <script>

        function totalAmount(){
            var t = 0;
            $('.amount').each(function(i,e){
                var amt = $(this).val()-0;
                t += amt;
            });
            $('#total_amount').val(t);

        }
        $(function () {
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
                    '<td><input type="number" id="stock_qty_'+n+'" class="stock_qty form-control" name="stock_qty[]" readonly></td>' +
                    '<td><input type="number" min="1" max="" class="qty form-control" name="qty[]" required></td>' +
                    '<td><input type="text" min="1" max="" class="qty form-control" name="production[]" required></td>' +
                    '<td><input type="text" id="price_'+n+'" min="1" max="" class="price form-control" name="price[]" value="" required></td>' +
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

                var price = tr.find('.price').val() - 0;


                var total = (qty * price);

                tr.find('.amount').val(total);
                //Total Price
                $(".amount").each(function() {
                    isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
                });
                var final_total = gr_tot;
                console.log(final_total);

                $("#total_amount").val(final_total);

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
                    url : "{{URL('product-production-relation-data')}}",
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
                        $("#price_"+current_row).val(res.data.mrp_price);
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
    </script>
@endpush


