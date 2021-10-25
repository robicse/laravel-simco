@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i> All Purchase Return Product</h1>
            </div>
{{--            <ul class="app-breadcrumb breadcrumb">--}}
{{--                <li class="breadcrumb-item"> <a href="{!! route('productSales.create') !!}" class="btn btn-sm btn-primary" type="button">Add Product Sales</a></li>--}}
{{--            </ul>--}}
        </div>
        <div class="col-md-12">
            <div class="tile">

                <h3 class="tile-title">Purchase Return Product Table</h3>
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th width="5%">SL NO</th>
                            <th>Invoice NO</th>
                            <th>Model</th>
                            <th>User</th>
{{--                            <th>Store</th>--}}
                            <th>Party</th>
                            <th>Payment Type</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($productPurchaseReturns as $key => $productPurchaseReturn)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $productPurchaseReturn->invoice_no}}</td>
                            <td>
                                @php
                                    $str_arr = explode (",", saleReturnsProductModels($productPurchaseReturn->id));
                                @endphp

                                @foreach($str_arr as $key => $str)
                                    @if(($key == 0 && count($str_arr) > 1))
                                        {{$str}},
                                    @elseif( (($key > 0 && $key < count($str_arr))) && ($key+1 != count($str_arr)))
                                        {{$str}},
                                    @else
                                        {{$str}}
                                    @endif
                                    <br/>
                                @endforeach
                            </td>
                            <td>{{ $productPurchaseReturn->user->name}}</td>
                            <td>{{ $productPurchaseReturn->party->name}}</td>
                            <td>
                                @php
                                    $transaction = \Illuminate\Support\Facades\DB::table('transactions')
                                  ->where('invoice_no',$productPurchaseReturn->invoice_no)
                                  ->where('ref_id',$productPurchaseReturn->id)
                                  ->first();

                                if($transaction){
                                    echo $transaction->payment_type;

                                    if($transaction->payment_type == 'Check'){
                                        echo "( Check Number: ". $transaction->check_number .")";
                                    }
                                }
                                @endphp

                            </td>
                            <td>{{ $productPurchaseReturn->total_amount}}</td>
                            <td>{{ $productPurchaseReturn->created_at}}</td>
                            <td class="d-inline-flex">
                                <a href="{{ route('productPurchaseReturn.show',$productPurchaseReturn->id) }}" class="btn btn-sm btn-info float-left" style="margin-left: 5px">Show</a>
                                <a href="{{ route('productPurchaseReturn.edit',$productPurchaseReturn->id) }}" class="btn btn-sm btn-primary float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>
                                <form method="post" action="{{ route('productPurchaseReturn.destroy',$productPurchaseReturn->id) }}" >
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-sm btn-danger" style="margin-left: 5px" type="submit" onclick="return confirm('You Are Sure This Delete !')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="tile-footer">
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection


