@extends('components/header')
@section('main')
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">YTD</h4>

        @php
            $all_year_data = [];
            $result = DB::table('truck_accounting')
                ->select(DB::raw('SUBSTRING(name, -4) as last_four_digits'), DB::raw('MAX(id) as id'))
                ->groupBy(DB::raw('SUBSTRING(name, -4)'))
                ->orderBy('name', 'DESC')
                ->get();
            if (isset($_GET['year'])) {
                $year_name = DB::table('truck_accounting')
                    ->where('id', $_GET['year'])
                    ->first();
                $all_year_data = DB::table('truck_accounting')
                    ->join('truck', 'truck_accounting.truck_id', '=', 'truck.id')
                    ->select('truck.truck_number as truck_number', 'truck.id as truck_id', 'truck_accounting.id as accounting_id')
                    ->where('name', $year_name->name)
                    ->get();
            } else {
                $result_ = DB::table('truck_accounting')
                    ->orderBy('name', 'DESC')
                    ->first();
                $all_year_data = DB::table('truck_accounting')
                    ->join('truck', 'truck_accounting.truck_id', '=', 'truck.id')
                    ->select('truck.truck_number as truck_number', 'truck.id as truck_id', 'truck_accounting.id as accounting_id')
                    ->where('name', $result_->name)
                    ->get();
            }
            $persentage = 8 / 100;
            $all_new_company = DB::table('company')
                ->where('is_deleted', 0)
                ->get();

            // dd($all_year_data);

        @endphp

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">YTD</h3>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <form action="">
                                <div class="text-right">
                                    <div class="d-flex">
                                        {{-- <select name="company" class="form-control" id="company" required>
                                            <option value="">Choose Company</option>
                                            @foreach ($all_new_company as $o)
                                                <option value="{{ $o->id }}"
                                                    @if (@request('company') == $o->id) selected @endif>{{ $o->company_name }}
                                                </option>
                                            @endforeach
                                        </select> --}}

                                        <select name="year" class="form-control" id="year" required>
                                            @foreach ($result as $item)
                                                <option value="{{ $item->id }}"
                                                    @if (@request('year') == $item->id) selected @endif>
                                                    {{ $item->last_four_digits }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <?php if(isset($_GET['year'])){ ?>
                    {{-- <button class="btn btn-primary" id="make_csv" style="float:right">Export</button> --}}
                    <div class="row">
                        @if (count($all_year_data) > 0)
                            {{-- <h5>Statement Name: {{$year->name}}</h5> --}}
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
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            $income__ = 0;
                                            $expense__ = 0;
                                            $total_extra = 0;
                                            $total_extra_sum = 0;
                                            ?>
                                            @foreach ($all_year_data as $d)
                                                @php
                                                    $income = DB::table('truck_income')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->where('accounting_id', $d->accounting_id)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) =' . $i)
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($income->total_amount)) {
                                                        $total_income = $income->total_amount;
                                                    } else {
                                                        $total_income = 0;
                                                    }

                                                    $income__ = $income__ + $total_income;

                                                @endphp
                                            @endforeach

                                            @php
                                                $income__ = $income__ * $persentage;
                                                $extra = DB::table('company_expense')
                                                    ->select('expense_name', DB::raw('SUM(amount) as total_amount'))
                                                    // ->where('company_id', $_GET['company'])
                                                    ->whereMonth('date', $i)
                                                    ->where('is_deleted', 0)
                                                    ->groupBy('expense_name')
                                                    ->get();
                                            @endphp
                                            <tr>
                                                <td>Income</td>
                                                <td>{{ $income__ }}</td>
                                            </tr>
                                            @foreach ($extra as $item)
                                                <tr>
                                                    <td>{{ $item->expense_name }}</td>
                                                    <td>{{ $item->total_amount }}</td>
                                                </tr>
                                                <?php $total_extra = $total_extra + $item->total_amount; ?>
                                            @endforeach
                                            <tr>
                                                <td><b>Net</b></td>
                                                <td><b>{{ $income__ - $total_extra }}</b></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        @else
                            <div class="col-md-12">
                                <h3 class="text-center">Nothing to display</h3>
                            </div>
                        @endif
                    </div>
                    <?php }else{ ?>

                    <div class="row">
                        @if (count($all_year_data) > 0)
                            {{-- <h5>Statement Name: {{$year->name}}</h5> --}}
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
                                        <tbody>
                                            <?php
                                            $total = 0;
                                            $income__ = 0;
                                            $expense__ = 0;
                                            $total_extra = 0;
                                            $total_extra_sum = 0;
                                            ?>
                                            @foreach ($all_year_data as $d)
                                                @php
                                                    $income = DB::table('truck_income')
                                                        ->select(DB::raw('MONTHNAME(CAST(date AS DATE)) as month'), DB::raw('SUM(amount) as total_amount'))
                                                        ->where('accounting_id', $d->accounting_id)
                                                        ->whereRaw('MONTH(CAST(date AS DATE)) =' . $i)
                                                        ->groupBy(DB::raw('MONTHNAME(CAST(date AS DATE))'))
                                                        ->orderBy('date', 'ASC')
                                                        ->first();
                                                    if (isset($income->total_amount)) {
                                                        $total_income = $income->total_amount;
                                                    } else {
                                                        $total_income = 0;
                                                    }

                                                    $extra = DB::table('company_expense')
                                                        ->whereMonth('date', $i)
                                                        ->where('is_deleted', 0)
                                                        ->sum('amount');

                                                    if (isset($extra)) {
                                                        $total_extra = $extra;
                                                    } else {
                                                        $total_extra = 0;
                                                    }

                                                    $income__ = $income__ + $total_income;

                                                @endphp
                                            @endforeach
                                            @php
                                                $income__ = $income__ * $persentage;
                                            @endphp
                                            <tr>
                                                <td>Income</td>
                                                <td>{{ $income__ }}</td>
                                            </tr>
                                            <tr>
                                                <td>Expense</td>
                                                <td>{{ $total_extra }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Net</b></td>
                                                <td><b>{{ $income__ - $total_extra }}</b></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } ?>
                        @else
                            <div class="col-md-12">
                                <h3 class="text-center">Nothing to display</h3>
                            </div>
                        @endif
                    </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        var choosed = "{{ @request('truck') }}";
        $(document).ready(function() {
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
