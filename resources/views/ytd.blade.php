@extends('components/header')
@section('main')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">YTD</h4>

        @php
            $accounting = [];
            $owners = DB::table('owners')
                ->where('is_deleted', 0)
                ->get();
            $result = DB::table('truck_accounting')
                ->select(DB::raw('SUBSTRING(name, -4) as last_four_digits'), DB::raw('MAX(id) as id'))
                ->groupBy(DB::raw('SUBSTRING(name, -4)'))
                ->orderBy('name', 'DESC')
                ->get();
            if (isset($_GET['company']) && isset($_GET['year'])) {
                $year = DB::table('truck_accounting')
                    ->where('id', $_GET['year'])
                    ->first();

                $data = DB::table('truck')
                    ->join('owners', 'truck.company_id', '=', 'owners.id')
                    ->join('truck_accounting', 'truck.id', '=', 'truck_accounting.truck_id')
                    ->where('owners.id', $_GET['company'])
                    ->where('truck_accounting.name', $year->name)
                    ->select('truck.truck_number as truck_number', 'truck_accounting.id as accounting_id')
                    ->get();
                // dd($data);
            }

            if (isset($_GET['truck']) && $_GET['truck'] != '' && isset($_GET['year']) && $_GET['year'] != '') {
                $nameofyear = DB::table('truck_accounting')
                    ->where('id', $_GET['year'])
                    ->first();
                    $truck_accounting_id = DB::table('truck_accounting')
                    ->where('truck_id', $_GET['truck'])
                    ->where('name', $nameofyear->name)
                    ->first();
                    $all_year_data = DB::table('truck_accounting')
                ->join('truck', 'truck_accounting.truck_id', '=', 'truck.id')
                ->select('truck.truck_number as truck_number', 'truck.id as truck_id', DB::raw('GROUP_CONCAT(truck_accounting.id) as accounting_ids'))
                ->where(DB::raw('SUBSTRING(name, -4)'), date('Y'))
                ->where('truck_id', $_GET['truck'])
                ->groupBy('truck.truck_number', 'truck.id')
                ->get();
            } else if (isset($_GET['year']) && $_GET['year'] != ''){
                $nameofyear = DB::table('truck_accounting')
                    ->where('id', $_GET['year'])
                    ->first();
                    $parts = explode(' - ', $nameofyear->name);
                    $year = $parts[2];
                $all_year_data = DB::table('truck_accounting')
                ->join('truck', 'truck_accounting.truck_id', '=', 'truck.id')
                ->select('truck.truck_number as truck_number', 'truck.id as truck_id', DB::raw('GROUP_CONCAT(truck_accounting.id) as accounting_ids'))
                ->where(DB::raw('SUBSTRING(name, -4)'), $year)
                ->groupBy('truck.truck_number', 'truck.id')
                ->get();
            } else {
                $all_year_data = DB::table('truck_accounting')
                    ->join('truck', 'truck_accounting.truck_id', '=', 'truck.id')
                    ->select('truck.truck_number as truck_number', 'truck.id as truck_id', DB::raw('GROUP_CONCAT(truck_accounting.id) as accounting_ids'))
                    ->where(DB::raw('SUBSTRING(name, -4)'), date('Y'))
                    ->groupBy('truck.truck_number', 'truck.id')
                    ->get();

            }
            // dd($all_year_data);
            $all_trucks_selcet = DB::table('truck')
                ->where('is_deleted', 0)
                ->get();
            $persentage = 8 / 100;

        @endphp

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">YTD</h3>
                <div class="col-md-12 mb-3">
                    {{-- <div class="col-md-8 offset-md-4"> --}}
                        <form action="">
                            {{-- <div class="text-right">
                                <div class="d-flex"> --}}
                                    <div class="row">
                                    <div class="col-md-5"></div>
                                        <div class="col-md-3"><select name="truck" class="form-control select2" id="truck">
                                            <option value="">Choose Truck</option>
                                            @foreach ($all_trucks_selcet as $o)
                                                <option value="{{ $o->id }}"
                                                    @if (@request('truck') == $o->id) selected @endif>{{ $o->truck_number }}
                                                </option>
                                            @endforeach
                                        </select></div>
                                        <div class="col-md-3"><select name="year" class="form-control select2" id="year">
                                            <option value="">Choose Year</option>
                                            @foreach ($result as $item)
                                                <option value="{{ $item->id }}"
                                                    @if (@request('year') == $item->id) selected @endif>
                                                    {{ $item->last_four_digits }}</option>
                                            @endforeach
                                        </select></div>
                                        <div class="col-md-1">
                                            <button class="btn btn-outline-primary">Filter</button>
                                        </div>
                                    </div>



                                    {{-- </div>
                                </div> --}}
                            </form>
                        {{-- </div> --}}
                    <hr>
                    {{-- <button class="btn btn-primary" id="make_csv" style="float:right">Export</button> --}}
                    @if (isset($_GET['truck']) && $_GET['truck'] != '' && isset($_GET['year']) && $_GET['year'] != '')
                        <div class="row">
                            <?php  for($i=1; $i<=12;$i++){ ?>

                            <div class="col-md-4 mb-5">
                                <h5 class="mb-0">
                                    @if ($i == 1)
                                        January
                                    @elseif($i == 2)
                                        February
                                    @elseif($i == 3)
                                        March
                                    @elseif($i == 4)
                                        April
                                    @elseif($i == 5)
                                        May
                                    @elseif($i == 6)
                                        June
                                    @elseif($i == 7)
                                        July
                                    @elseif($i == 8)
                                        August
                                    @elseif($i == 9)
                                        September
                                    @elseif($i == 10)
                                        October
                                    @elseif($i == 11)
                                        November
                                    @elseif($i == 12)
                                        December
                                    @endif

                                </h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Expense</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total_ = 0;
                                            // $expense = DB::table('truck_expense')
                                            //         ->whereMonth('date', '=', $i)
                                            //         ->where('accounting_id',$truck_accounting_id->id)
                                            //         ->get();

                                            $expense = DB::table('truck_expense')
                                                ->select('categories.name as c_name', 'truck_expense.description', DB::raw('SUM(amount) as amount'))
                                                ->join('categories', function ($join) {
                                                    $join->on('truck_expense.category', '=', 'categories.id');
                                                })
                                                ->whereMonth('date', '=', $i)
                                                ->where('accounting_id', @$truck_accounting_id->id)
                                                ->groupBy('description')
                                                ->get();                                            ?>
                                            @foreach ($expense as $d)
                                                <tr>
                                                    <td>{{ $d->c_name }}</td>
                                                    <td>{{ round($d->amount, 2) }}</td>
                                                </tr>
                                                <?php
                                                $total_ = $total_ + $d->amount;
                                                ?>
                                            @endforeach
                                            <tr>
                                                <td><b>Total:</b></td>
                                                <td><b>{{ $total_ }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    @elseif(isset($_GET['year']) && $_GET['year'] != '')
                        <div class="row">
                            <?php  for($i=1; $i<=12;$i++){ ?>

                            <div class="col-md-4 mb-5">
                                <h5 class="mb-0">
                                    @if ($i == 1)
                                        January
                                    @elseif($i == 2)
                                        February
                                    @elseif($i == 3)
                                        March
                                    @elseif($i == 4)
                                        April
                                    @elseif($i == 5)
                                        May
                                    @elseif($i == 6)
                                        June
                                    @elseif($i == 7)
                                        July
                                    @elseif($i == 8)
                                        August
                                    @elseif($i == 9)
                                        September
                                    @elseif($i == 10)
                                        October
                                    @elseif($i == 11)
                                        November
                                    @elseif($i == 12)
                                        December
                                    @endif
                                </h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Gross</th>
                                                <th>Expense</th>
                                                <th>NET</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; ?>
                                            @foreach ($all_year_data as $d)
                                                @php
                                                    $nameOfYear = DB::table('truck_accounting')
                                                        ->where('id', $_GET['year'])
                                                        ->first();
                                                    $parts = explode(' - ', $nameOfYear->name);

                                                    // The year is the third element in the exploded array
                                                    $year = $parts[2];

                                                    // Explode the comma-separated string into an array of accounting IDs
                                                    $accountingIds = explode(',', $d->accounting_ids);
                                                    $income = DB::table('truck_income')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->where('date', 'LIKE', $year . '%')
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($income->total_amount)) {
                                                        $total_income = $income->total_amount;
                                                    } else {
                                                        $total_income = 0;
                                                    }
                                                    $expense = DB::table('truck_expense')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->where('date','LIKE', $year . '%')
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($expense->total_amount)) {
                                                        $total_expense = $expense->total_amount;
                                                    } else {
                                                        $total_expense = 0;
                                                    }

                                                    $net = DB::table('truck_expense')
                                                        ->select(DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->where('date','LIKE', $year . '%')
                                                        ->where('category', 13)
                                                        ->first();
                                                        // if ($i == '03') {
                                                        //     dd($net);
                                                        // }
                                                        if (isset($net->total_amount)) {
                                                        $net_amount = $net->total_amount;
                                                    } else {
                                                        $net_amount = 0;
                                                    }
                                                @endphp
                                                @if ($total_income != 0 && $total_expense != 0)
                                                <tr>
                                                    <td>{{ $d->truck_number }}</td>
                                                    <td>{{ $total_income }}</td>
                                                    <td>{{ $total_expense }}</td>
                                                    <td>{{ $net_amount }}</td>
                                                    {{-- <td>{{ $total_income - $total_expense }}</td> --}}
                                                    <td>{{ $persentage * $total_income }}</td>
                                                </tr>
                                                <?php $total_ = $persentage * $total_income;
                                                $total = $total + $total_;
                                                ?>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><b>Total:</b></td>
                                                <td><b>{{ $total }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    @else
                        <div class="row">
                            <?php  for($i=1; $i<=12;$i++){ ?>

                            <div class="col-md-4 mb-5">
                                <h5 class="mb-0">
                                    @if ($i == 1)
                                        January
                                    @elseif($i == 2)
                                        February
                                    @elseif($i == 3)
                                        March
                                    @elseif($i == 4)
                                        April
                                    @elseif($i == 5)
                                        May
                                    @elseif($i == 6)
                                        June
                                    @elseif($i == 7)
                                        July
                                    @elseif($i == 8)
                                        August
                                    @elseif($i == 9)
                                        September
                                    @elseif($i == 10)
                                        October
                                    @elseif($i == 11)
                                        November
                                    @elseif($i == 12)
                                        December
                                    @endif
                                </h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Gross</th>
                                                <th>Expense</th>
                                                <th>NET</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; ?>
                                            @foreach ($all_year_data as $d)
                                                @php
                                                    // Explode the comma-separated string into an array of accounting IDs
                                                    $accountingIds = explode(',', $d->accounting_ids);
                                                    $income = DB::table('truck_income')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($income->total_amount)) {
                                                        $total_income = $income->total_amount;
                                                    } else {
                                                        $total_income = 0;
                                                    }
                                                    $expense = DB::table('truck_expense')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($expense->total_amount)) {
                                                        $total_expense = $expense->total_amount;
                                                    } else {
                                                        $total_expense = 0;
                                                    }
                                                    $net = DB::table('truck_expense')
                                                        ->select(DB::raw('SUM(amount) as total_amount'))
                                                        ->whereIn('accounting_id', $accountingIds)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) = ' . $i)
                                                        ->where('date','LIKE', date("Y") . '%')
                                                        ->where('category', 13)
                                                        ->first();
                                                        if (isset($net->total_amount)) {
                                                        $net_amount = $net->total_amount;
                                                    } else {
                                                        $net_amount = 0;
                                                    }
                                                @endphp
                                                @if ($total_income != 0 && $total_expense != 0)
                                                    <tr>
                                                        <td>{{ $d->truck_number }}</td>
                                                        <td>{{ $total_income }}</td>
                                                        <td>{{ $total_expense }}</td>
                                                        {{-- <td>{{ $total_income - $total_expense }}</td> --}}
                                                        <td>{{ $net_amount }}</td>
                                                        <td>{{ $persentage * $total_income }}</td>
                                                    </tr>
                                                    <?php $total_ = $persentage * $total_income;
                                                    $total = $total + $total_;
                                                    ?>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><b>Total:</b></td>
                                                <td><b>{{ $total }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('public') }}/assets/vendor/libs/select2/select2.js"></script>
    <script>
        var choosed = "{{ @request('truck') }}";
        $(document).ready(function() {
            $(".select2").select2();
            // getTrucks();
        });
        // $('#company').on('change', function(){
        //     getTrucks();
        // });
        // $('#company').on('change', function(){
        //     getTrucks();
        // });
        $('#make_csv').on('click', function() {
            var company = $('#company').val();
            var year = $('#year').val();
            window.location = "{{ url('make/csv') }}/" + company + '/' + year;
        })

        function getTrucks() {
            var id = $('#company option:selected').val();
            $('#truck').html(`<option value="">Choose Truck</option>`);
            // alert(id);
            $.ajax({
                type: 'ajax',
                method: 'POST',
                data: {
                    'id': id
                },
                url: "{{ url('getCompanyTrucks') }}",
                success: function(res) {
                    if (res) {
                        res.forEach(t => {
                            // console.log(res.id, choosed);
                            if (t.id == choosed) {
                                var html = `<option value="` + t.id + `" selected>` + t.truck_number +
                                    `</option>`;
                            } else {
                                var html = `<option value="` + t.id + `">` + t.truck_number +
                                    `</option>`;
                            }
                            $('#truck').append(html);
                        });
                    }
                }
            });
        }
    </script>
@endsection
