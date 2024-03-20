@extends('components/header')
@section('main')
<link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">YTD</h4>

@php
$accounting = [];
$owners = DB::table('owners')->where('is_deleted', 0)->get();

// if(request()->has('company')){
//     $dispatch = DB::table('truck_dispatch as td')->join('truck as t', 't.id', '=', 'td.truck_id')->join('truck_accounting as ta', 'ta.dispatch_id', '=', 'td.id');
//     if(request()->has('truck')){
//         // $trucks = [(Int)request('truck')];
//         $dispatch = $dispatch->where('t.id', request('truck'));
//     }else{
//         $trucks = DB::table('truck')->where('company_id', request('company'))->pluck('id')->toArray();
//         $dispatch = $dispatch->whereIn('t.id', $trucks);
//     }
//     $accounts = $dispatch->pluck('ta.id')->toArray();
//     $totalIncome = DB::table('truck_income')
//     ->whereIn('accounting_id', $accounts)
//     ->sum('amount');
//     $totalExpense = DB::table('truck_expense')
//     ->whereIn('accounting_id', $accounts)
//     ->sum('amount');
//     $totalEight = DB::table('truck_expense')
//     ->where('description', '8%')
//     ->whereIn('accounting_id', $accounts)
//     ->sum('amount');
//     $totalThree = DB::table('truck_expense')
//     ->where('description', '3%')
//     ->whereIn('accounting_id', $accounts)
//     ->sum('amount');
//     $accounting = $dispatch->select('ta.name', 't.truck_number', 'ta.id')->paginate(3);
// }
@endphp
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">YTD</h3>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <form action="">
                                <div class="text-right">
                                    <div class="d-flex">
                                        <select name="company" class="form-control" id="company" required>
                                            <option value="">Choose Company</option>
                                            @foreach ($owners as $o)
                                            <option value="{{$o->id}}" @if(@request('company') == $o->id) selected @endif>{{$o->company_name}}</option>
                                            @endforeach
                                        </select>
                                        <select name="truck" class="form-control" id="truck" required>
                                            <option value="">Choose Year</option>
                                        </select>
                                        <button class="btn btn-outline-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><hr>
                    <div class="row">
                        @if(count($accounting) > 0)
                            @foreach ($accounting as $a)
                            <div class="col-md-12">
                                <h4>Truck #: {{$a->truck_number}}</h4>
                                <h5>Statement Name: {{$a->name}}</h5>
                            </div>
                            @php
                                $income = DB::table('truck_income')->where('accounting_id', $a->id)->get();
                                $expense = DB::table('truck_expense')->where('accounting_id', $a->id)->get();
                            @endphp
                                <div class="col-md-12">
                                    <h5>Income</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Truck #</th>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($income) > 0)
                                                @foreach ($income as $i)
                                                @php
                                                    list($weekNumber, $totalWeeks, $year) = explode(" - ", $a->name);
                                                    $startDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-1"));
                                                    $endDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-7"));
                                                @endphp
                                                    <tr>
                                                        <td>{{$a->truck_number}}</td>
                                                        <td>{{$startDate}}, {{$endDate}}</td>
                                                        <td>{{$i->description}}</td>
                                                        <td>{{$i->amount}}</td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="4"><p class="text-center">Nothing to display</p></td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3 mb-3">
                                    <h5>Expense</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Truck #</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($expense) > 0)
                                                @foreach ($expense as $e)
                                                    <tr>
                                                        <td>{{$a->truck_number}}</td>
                                                        <td>{{$e->description}}</td>
                                                        <td>{{$e->amount}}</td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="4"><p class="text-center">Nothing to display</p></td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div><hr>
                                </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Total 8%</th>
                                                    <td>{{$totalEight}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total 3%</th>
                                                    <td>{{$totalThree}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Income</th>
                                                    <td>{{$totalIncome}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Expense</th>
                                                    <td>-{{$totalExpense}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Net Income</th>
                                                    <td><b>{{$totalIncome-$totalExpense}}</b></td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                        <div class="col-md-12">
                            <h3 class="text-center">Nothing to display</h3>
                        </div>
                        @endif
                    </div>

                </div>

                <div class="col-md-12 mt-3">
                    @if(count($accounting) > 0)
                    {{ @$accounting->appends(request()->input())->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    var choosed = "{{@request('truck')}}";
    $(document).ready(function(){
        getTrucks();
    });
    $('#company').on('change', function(){
        getTrucks();
    });

    function getTrucks(){
        var id = $('#company option:selected').val();
        $('#truck').html(`<option value="">Choose Truck</option>`);
        // alert(id);
        $.ajax({
            type: 'ajax',
            method: 'POST',
            data: {'id': id},
            url: "{{url('getCompanyTrucks')}}",
            success: function(res){
                if(res){
                    res.forEach(t => {
                        // console.log(res.id, choosed);
                        if(t.id == choosed){
                            var html = `<option value="`+t.id+`" selected>`+t.truck_number+`</option>`;
                        }else{
                            var html = `<option value="`+t.id+`">`+t.truck_number+`</option>`;
                        }
                        $('#truck').append(html);
                    });
                }
            }
        });
    }
</script>
@endsection
