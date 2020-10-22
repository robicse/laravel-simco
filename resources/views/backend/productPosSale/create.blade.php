@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="col-md-12">
            <div class="tile">

                <form method="post" action="">
                    @csrf
                    {{ csrf_field() }}
{{--                    <input type="hidden" name="idpenjualan" value="">--}}
                    <div class="form-group row">
                        <label for="totalrp" class="col-md-2 control-label">Product Code</label>
                        <div class="col-md-10  input-group">
                            <input id="kode" type="text" class="form-control" name="kode" autofocus required>
                            <span class="input-group-btn">
                                <button onclick="showProduct()" type="button" class="btn btn-info">Show Product</button>
                            </span>
                        </div>
                    </div>
                </form>


{{--                <form class="form-keranjang">--}}
{{--                    {{ csrf_field() }} {{ method_field('PATCH') }}--}}
{{--                    <table class="table table-striped tabel-penjualan">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th width="30">No</th>--}}
{{--                            <th>Product Code</th>--}}
{{--                            <th>Product Name</th>--}}
{{--                            <th align="right">Price</th>--}}
{{--                            <th>Quantity</th>--}}
{{--                            <th>Discount</th>--}}
{{--                            <th align="right">Sub Total</th>--}}
{{--                            <th width="100">Action</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody></tbody>--}}
{{--                    </table>--}}
{{--                </form>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-8">--}}
{{--                        <div id="tampil-bayar" style="background: #dd4b39; color: #fff; font-size: 80px; text-align: center; height: 120px"></div>--}}
{{--                        <div id="tampil-terbilang" style="background: #3c8dbc; color: #fff; font-size: 25px; padding: 10px"></div>--}}
{{--                    </div>--}}

{{--                    <div class="col-md-4">--}}
{{--                        <form class="form form-horizontal form-penjualan" method="post" action="transaksi/simpan">--}}
{{--                            {{ csrf_field() }}--}}
{{--                            <input type="hidden" name="idpenjualan" value="">--}}
{{--                            <input type="hidden" name="total" id="total">--}}
{{--                            <input type="hidden" name="totalitem" id="totalitem">--}}
{{--                            <input type="hidden" name="bayar" id="bayar">--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="totalrp" class="col-md-4 control-label">Total</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <input type="text" class="form-control" id="totalrp" readonly>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="member" class="col-md-4 control-label">Customer</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <div class="input-group">--}}
{{--                                        <input id="member" type="text" class="form-control" name="member" value="0">--}}
{{--                                        <span class="input-group-btn">--}}
{{--                                          <button onclick="showMember()" type="button" class="btn btn-info">...</button>--}}
{{--                                        </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="diskon" class="col-md-4 control-label">Discount</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <input type="text" class="form-control" name="diskon" id="diskon" value="0" readonly>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="bayarrp" class="col-md-4 control-label">Total</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <input type="text" class="form-control" id="bayarrp" readonly>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="diterima" class="col-md-4 control-label">Paid</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <input type="number" class="form-control" value="0" name="diterima" id="diterima">--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="kembali" class="col-md-4 control-label">Due</label>--}}
{{--                                <div class="col-md-8">--}}
{{--                                    <input type="text" class="form-control" id="kembali" value="0" readonly>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="box-footer">--}}
{{--                                <button type="submit" class="btn btn-primary pull-right simpan"><i class="fa fa-floppy-o"></i> Save</button>--}}
{{--                            </div>--}}

{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="col-md-12" id="newform"></div>
            </div>
        </div>
    </main>
    @include('backend.productPosSale.product')
    @include('backend.productPosSale.member')
@endsection

@push('js')
    <script>

        function loadData(id){
            // alert(id);
            $.ajax({
                url : "{{ URL('/selectedform') }}/" + id,
                type: "GET",
                dataType: "json",
                /*dataType: "html",*/
                success: function(data)
                {
                    console.log(data);
                    $('#newform').html(data);
                    /*$('#btnSave').hide();*/
                    /*console.log(data);*/
                },
                /*error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }*/
                error: function (data) {
                    console.log(data);
                }
            });
        }


        $(function(){
            $('.tabel-produk').DataTable();

            loadData(product_code=null)

            // var i = 1;
            $('#kode').change(function(){
                // console.log(i++)
                var product_code = $('#kode').val();
                // console.log(product_code);


                if(product_code){
                    $.ajax({
                        url : "{{URL('add-to-cart')}}",
                        method : "get",
                        data : {
                            product_code : product_code
                        },
                        success : function (res){
                            console.log(res)
                            $('#kode').val('').focus();
                            loadData(product_code)
                        },
                        error : function (err){
                            console.log(err)
                        }
                    })
                }else{
                    alert('No Product Code Found!');
                    //location.reload();
                }

            });

        });


        function deleteCart(rowId) {
            //var product_code = "";
            if (confirm("Are you sure, delete this item!")) {
                $.ajax({
                    url: "{{ URL('/delete-cart-product') }}/" + rowId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        console.log(data)
                        loadData(product_code)
                    },
                    error: function (err) {
                        console.log(err)
                    }
                });
            }
        }

        function deleteAllCart() {
            //var product_code = "";
            if (confirm("Are you sure, delete all item!")) {
                $.ajax({
                    url: "{{ URL('/delete-all-cart-product') }}",
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        console.log(data)
                        loadData(product_code)
                    },
                    error: function (err) {
                        console.log(err)
                    }
                });
            }
        }




        function showProduct(){
            $('#modal-produk').modal('show');
        }

        function selectItem(kode){
            $('#kode').val(kode);
            $('#modal-produk').modal('hide');
        }

        function showMember(){
            $('#modal-member').modal('show');
        }

        function selectMember(kode){
            $('#modal-member').modal('hide');
            $('#member').val(kode);
            $('#diterima').val(0).focus().select();
        }

    </script>
@endpush()
