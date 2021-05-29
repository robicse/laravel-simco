@extends('backend._partial.dashboard')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class=""></i>Loss/Profit</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">
                    @if($start_date != '' && $end_date != '')
                        <a class="btn btn-warning" href="{{ url('loss-profit-filter-export/'.$start_date."/".$end_date) }}">Export Data</a>
                    @else
                        <a class="btn btn-warning" href="{{ route('loss.profit.export') }}">Export Data</a>
                    @endif
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Loss/Profit Table</h3>
                <form class="form-inline" action="{{ route('transaction.lossProfit') }}">
                    <div class="form-group col-md-4">
                        <label for="start_date">Start Date:</label>
                        <input type="text" name="start_date" class="datepicker form-control" value="{{$start_date}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="end_date">End Date:</label>
                        <input type="text" name="end_date" class="datepicker form-control" value="{{$end_date}}">
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <a href="{!! route('transaction.lossProfit') !!}" class="btn btn-primary" type="button">Reset</a>
                    </div>
                </form>
                <div>&nbsp;</div>
                @if(!empty($stores))
                    @foreach($stores as $store)
                        <div class="col-md-12">
                            <h1 class="text-center">{{$store->name}}</h1>
                            <table>
                                <thead>
                                @php
                                    $loss_profit = loss_profit($store->id,$start_date,$end_date);
                                    $total_expense = total_expense($store->id,$start_date,$end_date);
                                @endphp
                                <tr>
                                    <th colspan="10">Sum Product Based Loss/Profit: </th>
                                    <th>
                                        @if($loss_profit > 0)
                                            Profit: {{number_format($loss_profit, 2, '.', '')}}
                                        @else
                                            Loss: {{number_format($loss_profit, 2, '.', '')}}
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Expense:</th>
                                    <th>
                                        {{number_format($total_expense, 2, '.', '')}}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Final Loss/Profit:</th>
                                    <th>
                                        @if($loss_profit > 0)
                                            Profit: {{number_format($loss_profit - $total_expense, 2, '.', '')}}
                                        @else
                                            Loss: {{number_format($loss_profit + $total_expense, 2, '.', '')}}
                                        @endif
                                    </th>
                                </tr>
                                </thead>
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


