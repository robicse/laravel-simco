@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> All Replacement Sale Product</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"> <a href="{!! route('productSaleReplacement.create') !!}" class="btn btn-sm btn-primary" type="button">Add Product Sales</a></li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">

                <h3 class="tile-title">All Replacement Sale Product</h3>
                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                    <tr>
                        <th width="5%">#Id</th>
                        <th>Invoice</th>
                        <th>User</th>
                        <th>Store</th>
                        <th>Customer</th>
{{--                        <th>Product</th>--}}
{{--                        <th>Replace Qty</th>--}}
{{--                        <th>Action</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($productSaleReplacements as $key => $productSaleReplacement)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $productSaleReplacement->sale_invoice_no}}</td>
                        <td>{{ $productSaleReplacement->user->name}}</td>
                        <td>{{ $productSaleReplacement->store->name}}</td>
                        <td>{{ $productSaleReplacement->party->name}}</td>
{{--                        <td>{{ $productSaleReplacement->product->name}}</td>--}}
{{--                        <td>{{ $productSaleReplacement->replace_qty}}</td>--}}
{{--                        <td>--}}
{{--                            <a href="{{ route('productSaleReplacement.show',$productSaleReplacement->id) }}" class="btn btn-sm btn-info float-left">Show</a>--}}
{{--                            <a href="{{ route('productSaleReplacement.edit',$productSaleReplacement->id) }}" class="btn btn-sm btn-primary float-left"><i class="fa fa-edit"></i></a>--}}
{{--                            <form method="post" action="{{ route('productSaleReplacement.destroy',$productSaleReplacement->id) }}" >--}}
{{--                               @method('DELETE')--}}
{{--                                @csrf--}}
{{--                                <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('You Are Sure This Delete !')"><i class="fa fa-trash"></i></button>--}}
{{--                            </form>--}}
{{--                        </td>--}}
                    </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="tile-footer">
                </div>
            </div>

        </div>
    </main>
@endsection


