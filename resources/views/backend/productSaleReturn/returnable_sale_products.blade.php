@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            {{--<div>
                <h1><i class=""></i> Add Sales Product</h1>
            </div>--}}
            <!--<ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('productSales.index') }}" class="btn btn-sm btn-primary col-sm" type="button">All Sales Product</a>
                </li>
            </ul>-->
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Returnable Sales Product</h3>
                <div class="tile-body tile-footer">
                    <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th >ID</th>
                                    <th>Party</th>
                                    <th>Product</th>
{{--                                    <th>Category</th>--}}
{{--                                    <th>Sub Category</th>--}}
                                    <th>Brand</th>
{{--                                    <th>Return Condition</th>--}}
                                    <th>Received Qty</th>
                                    <th>Price</th>
                                    <th style="text-align:center">Returned</th>
                                </tr>
                            </thead>
                            <tbody class="neworderbody">
                            @foreach($returnable_sale_products as $key => $returnable_sale_product)
                                @php
                                    $key += 1;
                                @endphp
                                <tr>
                                    <td width="5%" class="no">1</td>
                                    <td>
                                        @php
                                            $party_name = DB::table('product_sales')
                                                ->join('product_sale_details', 'product_sales.id', '=', 'product_sale_details.product_sale_id')
                                                ->join('parties', 'parties.id', '=', 'product_sales.party_id')
                                                ->where('product_sale_details.id',$returnable_sale_product->id)
                                                ->select('parties.name')
                                                ->first();
                                            //dd($party_name);
                                        @endphp
                                        {{$party_name->name}}
                                    </td>
                                    <td>{{$returnable_sale_product->product->name}}</td>
{{--                                    <td>{{$returnable_sale_product->product->product_category->name}}</td>--}}
{{--                                    <td>{{$returnable_sale_product->product->product_sub_category ? $returnable_sale_product->product->product_sub_category->name : ''}}</td>--}}
                                    <td>{{$returnable_sale_product->product->product_brand->name}}</td>
{{--                                    <td>{{$returnable_sale_product->return_type}}</td>--}}
                                    <td>{{$returnable_sale_product->qty}}</td>
                                    <td>{{$returnable_sale_product->price}}</td>
                                    <td>
                                        <form method="post" action="{{route('sale.product.return')}}" class="row">
                                            @csrf
                                            <div class="form-group col-md-6">
                                                <label class="control-label">Qty  <small class="text-danger">*</small></label>
                                                <input class="form-control" type="hidden" name="qty" id="qty_{{$key}}" value="{{$returnable_sale_product->qty}}">
                                                <input class="form-control" type="text" name="return_qty" id="return_qty_{{$key}}" onkeyup="return_qty1({{$key}},this);" placeholder="Enter return qty">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label">Amount  <small class="text-danger">*</small></label>
                                                <input type="number" min="1" name="total_amount" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label">Payment Type  <small class="text-danger">*</small></label>
                                                    <select name="payment_type" id="payment_type_{{$key}}" class="form-control" onchange="productType({{$key}},this)">
                                                        <option value="">Select One</option>
                                                        <option value="cash">cash</option>
                                                        <option value="check">check</option>
                                                    </select>
                                                    <span>&nbsp;</span>
                                                    <input type="text" name="check_number" id="check_number_{{$key}}" class="form-control" placeholder="Check Number" readonly="readonly">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="control-label">Reason</label>
                                                    <textarea class="form-control" name="reason"></textarea>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="control-label"></label>
                                                    <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tfoot>
                        </table>

                </div>
                <div class="tile-footer">
                </div>
            </div>
        </div>
    </main>

@endsection

@push('js')
    <script>

        // ajax
        function return_qty1(row,sel) {
            console.log('ooo');
            var current_row = row;
            var current_return_qty = sel.value;
            console.log(current_row);
            console.log(current_return_qty);
            //var current_product_id = $('#product_id_'+current_row).val();

            var current_sale_qty = $('#qty_'+current_row).val();
            if(current_return_qty > current_sale_qty){
                alert('You have limit cross of stock qty!');
                $('#return_qty_'+current_row).val(0);
            }
        }

        function productType(row,sel){
            var current_row = row;
            var arr = $('#payment_type_'+current_row).val();
            if(arr == "check"){ $("#check_number_"+current_row).removeAttr("readonly"); }
            if(arr == "cash"){ $("#check_number_"+current_row).attr("readonly", "readonly"); }
        }

    </script>
@endpush


