@extends('components/header')
@section('main')
<link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">YTD</h4>

@php
$accounting = [];
$owners = DB::table('owners')->where('is_deleted', 0)->get();
$result = DB::table('truck_accounting')
    ->select(DB::raw('SUBSTRING(name, -4) as last_four_digits'), DB::raw('MAX(id) as id'))
    ->groupBy(DB::raw('SUBSTRING(name, -4)'))
    ->orderBy('name','DESC')
    ->get();
    if(isset($_GET['company']) && isset($_GET['year'])){
        $year=DB::table('truck_accounting')->where('id',$_GET['year'])->first();

        $data=DB::table('truck')
        ->join('owners','truck.company_id','=','owners.id')
        ->join('truck_accounting','truck.id','=','truck_accounting.truck_id')
        ->where('owners.id',$_GET['company'])
        ->where('truck_accounting.name',$year->name)
        ->select('truck.truck_number as truck_number','truck_accounting.id as accounting_id')
        ->get();
    }
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
                                        
                                        <select name="year" class="form-control" id="year" required>
                                            <option value="">Choose Year</option>
                                            @foreach ($result as $item)
                                                <option value="{{$item->id}}" @if(@request('year') == $item->id) selected @endif>{{$item->last_four_digits}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><hr>
                    <?php if(isset($data)){ ?>
                    <button class="btn btn-primary" id="make_csv" style="float:right">Export</button>
                    <div class="row">
                        @if(count($data) > 0)
                        <h5>Statement Name: {{$year->name}}</h5>
                        @foreach ($data as $d)
                        <div class="col-md-12">
                            <h4>Truck #: {{$d->truck_number}}</h4>
                        </div>

                            @php
                               $income = DB::table('truck_income')
                                    ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                    ->where('accounting_id', $d->accounting_id)
                                    ->whereRaw('MONTH(CAST(date AS DATE)) = 1') // Filter for January
                                    ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                    ->orderBy('date', 'ASC')
                                    ->get();

                                    $expense = DB::table('truck_expense')
                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                        ->where('accounting_id', $d->accounting_id)
                                        ->whereRaw('MONTH(CAST(date AS DATE)) = 1') // Filter for January
                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                        ->orderBy('date', 'ASC')
                                        ->get();

                            @endphp
                                <div class="col-md-12">
                                    <h5>January</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Truck #</th>
                                                            <th>Month</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(count($income) > 0)
                                                        @foreach ($income as $i)
                                                      
                                                            <tr>
                                                                <td>{{$d->truck_number}}</td>
                                                                <td>{{$i->month}}</td>
                                                                <td>{{$i->total_amount}}</td>
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
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3 mb-3">
                                    <h5>Expense</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Truck #</th>
                                                    <th>Month</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($expense) > 0)
                                                @foreach ($expense as $e)
                                                    <tr>
                                                        <td>{{$d->truck_number}}</td>
                                                        <td>{{$e->month}}</td>
                                                        <td>{{$e->total_amount}}</td>
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
                           
                        @else
                        <div class="col-md-12">
                            <h3 class="text-center">Nothing to display</h3>
                        </div>
                        @endif
                    </div>
                    <?php } ?>

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
        // getTrucks();
    });
    // $('#company').on('change', function(){
    //     getTrucks();
    // });
     // $('#company').on('change', function(){
    //     getTrucks();
    // });
$('#make_csv').on('click',function(){
    var company = $('#company').val();
    var year = $('#year').val();
    window.location="{{url('make/csv')}}/"+company+'/'+year;
})
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
