@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> Stock Summary</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
{{--                <li class="breadcrumb-item"><a class="btn btn-warning" href="{{ route('stock.export') }}">Export Data</a></li>--}}
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Stock Summary</h3>
                @if(!empty($stores))
                    @foreach($stores as $store)
                        <div class="col-md-12">
                            <h1 class="text-center">{{$store->name}}</h1>
                        </div>
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="5%">#Id</th>
{{--                                    <th width="10%">Store</th>--}}
                                    <th width="15%">Product Type</th>
                                    <th width="12%">Brand</th>
                                    <th width="12%">Product</th>
                                    <th width="12%">Current Stock</th>
{{--                                    <th width="12%">Date</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    //$stocks = \App\Stock::where('store_id',$store->id)->latest()->groupBy('product_id')->get();

                                    $stocks = \App\Stock::where('store_id',$store->id)
                                    ->whereIn('id', function($query) {
                                           $query->from('stocks')->groupBy('product_id')->selectRaw('MAX(id)');
                                        })->latest('id')->get();

                                @endphp
                                @foreach($stocks as $key => $stock)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
{{--                                        <td>{{ $stock->store->name}}</td>--}}
                                        <td>{{ $stock->product->product_type}}</td>
                                        <td>{{ $stock->product->product_brand->name}}</td>
                                        <td>{{ $stock->product->name}}</td>
                                        <td>{{ $stock->current_stock}}</td>
{{--                                        <td>{{ $stock->date}}</td>--}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="tile-footer">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </main>
@endsection


