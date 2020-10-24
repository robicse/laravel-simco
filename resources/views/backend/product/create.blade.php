@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> Add Product</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary col-sm" type="button">All Product</a>
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Add Product</h3>
                <div class="tile-body tile-footer">
                    @if(session('response'))
                        <div class="alert alert-success">
                            {{ session('response') }}
                        </div>
                    @endif
                    <form method="post" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Product Type <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <select name="product_type" id="product_type" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Finish Goods">Finish Goods</option>
                                    <option value="Raw Materials">Raw Materials</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Name <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" placeholder="Name" name="name">
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Product Code <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control{{ $errors->has('product_code') ? ' is-invalid' : '' }}" type="text" placeholder="Product Code" name="product_code">
                                @if ($errors->has('product_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Product Category <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <select name="product_category_id" id="product_category_id" class="form-control">
                                    <option value="">Select One</option>
                                    @foreach($productCategories as $productCategory)
                                        <option value="{{$productCategory->id}}">{{$productCategory->name}}</option>
                                    @endforeach()
                                </select>
                                @if ($errors->has('product_category_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_category_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Product Sub Category <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <select name="product_sub_category_id" id="product_sub_category_id" class="form-control">
                                    <option value="">Select One</option>
                                    @foreach($productSubCategories as $productSubCategory)
                                        <option value="{{$productSubCategory->id}}">{{$productSubCategory->name}}</option>
                                    @endforeach()
                                </select>
                                @if ($errors->has('product_sub_category_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_sub_category_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Product Brand <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <select name="product_brand_id" id="product_brand_id" class="form-control">
                                    <option value="">Select One</option>
                                    @foreach($productBrands as $productBrand)
                                        <option value="{{$productBrand->id}}">{{$productBrand->name}}</option>
                                    @endforeach()
                                </select>
                                @if ($errors->has('product_brand_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_brand_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Model</label>
                            <div class="col-md-8">
                                <input class="form-control{{ $errors->has('model') ? ' is-invalid' : '' }}" type="text" placeholder="Model" name="model">
                                @if ($errors->has('model'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('model') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Description</label>
                            <div class="col-md-8">
                                <textarea rows="4" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" type="text" placeholder="description" name="description"> </textarea>
                                @if ($errors->has('description'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Image <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <input type="file" id="image" name="image" class="form-control-file">
{{--                                @if ($errors->has('image'))--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $errors->first('image') }}</strong>--}}
{{--                                    </span>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3 text-right">Status <span style="color: red">*</span></label>
                            <div class="col-md-8">
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Stock In</option>
                                    <option value="0">Stock Out</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-md-3"></label>
                            <div class="col-md-8">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save Product</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tile-footer">
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script>
        $('#product_category_id').change(function(){
            var product_category_id = $(this).val();
            //alert(product_category_id);
            $.ajax({
                url : "{{URL('sub-category-list')}}",
                method : "get",
                data : {
                    product_category_id : product_category_id
                },
                success : function (res){
                    console.log(res)
                    $('#product_sub_category_id').html(res.data)
                },
                error : function (err){
                    console.log(err)
                }
            })
        })
    </script>
@endpush


