@extends('components/header')
@section('main')
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Dispatch Statement</h4>

        @php
            use Carbon\Carbon;
            $week_no = '';
            $year_no = '';
            if (request()->has('weeks') && request('weeks') != '') {
                $week_name = DB::table('truck_accounting')
                    ->where('id', request('weeks'))
                    ->first();
                if (!empty($week_name)) {
                    $name = $week_name->name;
                    $weekParts = explode('-', $name);
                    // dd($weekParts);
                    if (count($weekParts) == 3) {
                        $week_no = $weekParts[0];
                        $year_no = $weekParts[2];
                    }
                } else {
                    $name = '';
                }
            } else {
                $currentDate = new DateTime();
                $weekNumber = $currentDate->format('W');
                $yearNumber = $currentDate->format('o');
                $firstDay = new DateTime($yearNumber . '-01-01');
                $lastDay = new DateTime($yearNumber . '-12-31');
                $totalWeeks = 52;
                $name = $weekNumber . ' - ' . $totalWeeks . ' - ' . $yearNumber;

                $week_no = $currentDate->format('W');
                $year_no = $currentDate->format('o');
            }
            $result = [];
            $all_weeks = [];
            $truckIds = [];
            if (isset($_GET['dispatcher'])) {
                $item = DB::table('dispatchers')
                    ->where('id', $_GET['dispatcher'])
                    ->first();

                $numbers_string = $item->truck_id;
                $dispatcher_id = $item->id;
                $dispatcher_percentage = $item->salary;
                $numbers_array = array_map('intval', explode(',', $numbers_string));
                $result = [];
                $result_expense = [];
                if (request()->has('weeks') && request('weeks') != '') {
                    foreach ($numbers_array as $truck_id) {
                        $all_trucks_income = DB::table('truck_accounting')
                            ->leftjoin('truck_income', 'truck_accounting.id', '=', 'truck_income.accounting_id')

                            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id', DB::raw('COALESCE(SUM(truck_income.amount), 0) as income_amount'))
                            ->where('truck_accounting.name', $name)
                            ->where('truck_accounting.is_deleted', 0)
                            ->where('truck_accounting.truck_id', $truck_id)
                            ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id')
                            ->first();
                        $all_trucks_expense = DB::table('truck_accounting')
                            ->leftjoin('truck_expense', 'truck_accounting.id', '=', 'truck_expense.accounting_id')
                            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id', DB::raw('COALESCE(SUM(truck_expense.amount), 0) as expense_amount'))
                            ->where('truck_accounting.name', $name)
                            ->where('truck_accounting.is_deleted', 0)
                            ->where('truck_accounting.truck_id', $truck_id)
                            ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id')
                            ->first();

                        if (isset($all_trucks_income) || isset($all_trucks_expense)) {
                            $result[] = [
                                'net_amount' => @$all_trucks_income->income_amount,
                                'truck_id' => $truck_id,
                                'name' => $name,
                            ];
                        }
                        $truckIds[] = [
                            'truck_id' => $truck_id,
                        ];

                        $all_trucks_expense = DB::table('extra_truck_expense')
                            ->where('is_deleted', 0)
                            ->where('truck_id', $truck_id)
                            ->where('week', $name)
                            ->first();
                        if (isset($all_trucks_expense)) {
                            $result_expense[] = [
                                'amount' => @$all_trucks_expense->amount,
                                'date' => @$all_trucks_expense->date,
                                'description' => @$all_trucks_expense->description,
                                'truck_id' => $truck_id,
                            ];
                        }
                    }
                } else {
                    foreach ($numbers_array as $truck_id) {
                        $all_trucks_income = DB::table('truck_accounting')
                            ->leftjoin('truck_income', 'truck_accounting.id', '=', 'truck_income.accounting_id')
                            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id', DB::raw('SUM(truck_income.amount) as income_amount'))
                            ->where('truck_accounting.name', $name)
                            ->where('truck_accounting.is_deleted', 0)
                            ->where('truck_accounting.truck_id', $truck_id)
                            ->whereBetween('truck_income.date', [
                                Carbon::now()
                                    ->startOfWeek(Carbon::SUNDAY)
                                    ->format('Y-m-d'),
                                Carbon::now()
                                    ->endOfWeek(Carbon::SATURDAY)
                                    ->format('Y-m-d'),
                            ])
                            ->groupBy('truck_accounting.truck_id')
                            ->first();

                        $all_trucks_expense = DB::table('truck_accounting')
                            ->leftjoin('truck_expense', 'truck_accounting.id', '=', 'truck_expense.accounting_id')

                            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id', DB::raw('COALESCE(SUM(truck_expense.amount), 0) as expense_amount'))
                            ->where('truck_accounting.name', $name)
                            ->where('truck_accounting.is_deleted', 0)
                            ->where('truck_accounting.truck_id', $truck_id)
                            ->whereBetween('truck_expense.date', [
                                Carbon::now()
                                    ->startOfWeek(Carbon::SUNDAY)
                                    ->format('Y-m-d'),
                                Carbon::now()
                                    ->endOfWeek(Carbon::SATURDAY)
                                    ->format('Y-m-d'),
                            ])
                            ->groupBy('truck_accounting.truck_id')
                            ->first();

                        if (isset($all_trucks_income) || isset($all_trucks_expense)) {
                            $result[] = [
                                'net_amount' => @$all_trucks_income->income_amount,
                                'truck_id' => $truck_id,
                                'name' => $name,
                            ];
                        }
                        $truckIds[] = [
                            'truck_id' => $truck_id,
                        ];

                        $all_trucks_expense = DB::table('extra_truck_expense')
                            ->where('is_deleted', 0)
                            ->where('truck_id', $truck_id)
                            ->whereBetween('date', [
                                Carbon::now()
                                    ->startOfWeek(Carbon::SUNDAY)
                                    ->format('Y-m-d'),
                                Carbon::now()
                                    ->endOfWeek(Carbon::SATURDAY)
                                    ->format('Y-m-d'),
                            ])
                            ->first();
                        if (isset($all_trucks_expense)) {
                            $result_expense[] = [
                                'amount' => @$all_trucks_expense->amount,
                                'date' => @$all_trucks_expense->date,
                                'description' => @$all_trucks_expense->description,
                                'truck_id' => $truck_id,
                            ];
                        }
                    }
                }
            } else {
                $all_trucks_income = DB::table('truck_accounting')
                    ->leftjoin('truck_income', 'truck_accounting.id', '=', 'truck_income.accounting_id')

                    ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id', DB::raw('COALESCE(SUM(truck_income.amount), 0) as income_amount'))
                    ->where('truck_accounting.name', $name)
                    ->where('truck_accounting.is_deleted', 0)
                    ->whereBetween('truck_income.date', [
                        Carbon::now()
                            ->startOfWeek(Carbon::SUNDAY)
                            ->format('Y-m-d'),
                        Carbon::now()
                            ->endOfWeek(Carbon::SATURDAY)
                            ->format('Y-m-d'),
                    ])
                    ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id')
                    ->get();

                $all_trucks_expense_ = DB::table('truck_accounting')
                    ->leftjoin('truck_expense', 'truck_accounting.id', '=', 'truck_expense.accounting_id')
                    ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id', DB::raw('COALESCE(SUM(truck_expense.amount), 0) as expense_amount'))
                    ->where('truck_accounting.name', $name)
                    ->where('truck_accounting.is_deleted', 0)
                    ->whereBetween('truck_expense.date', [
                        Carbon::now()
                            ->startOfWeek(Carbon::SUNDAY)
                            ->format('Y-m-d'),
                        Carbon::now()
                            ->endOfWeek(Carbon::SATURDAY)
                            ->format('Y-m-d'),
                    ])
                    ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id')
                    ->get();

                $result = $all_trucks_income->map(function ($income) use ($all_trucks_expense_) {
                    $expense = $all_trucks_expense_->where('id', $income->id)->first();
                    $income->net_amount = $income->income_amount;
                    return $income;
                });
                $all_trucks_expense = DB::table('extra_truck_expense')
                    ->where('is_deleted', 0)
                    ->whereBetween('date', [
                        Carbon::now()
                            ->startOfWeek(Carbon::SUNDAY)
                            ->format('Y-m-d'),
                        Carbon::now()
                            ->endOfWeek(Carbon::SATURDAY)
                            ->format('Y-m-d'),
                    ])
                    ->get();
            }
            if (count($truckIds) > 0) {
                $all_weeks = DB::table('truck_accounting')
                    ->select('id', 'name')
                    ->whereIn('truck_id', $truckIds)
                    ->groupBy('name')
                    ->get();
            }

            $total_income = 0;
            $total_expense = 0;
            $dispatcher_pay = 0;

            $all_trucks = DB::table('truck')
                ->where('is_deleted', 0)
                // ->whereIn('id',$numbers_array)
                ->get();
        @endphp

        @php
            $dispatchers = DB::table('dispatchers')
                ->where('is_deleted', 0)
                ->get();
        @endphp

        <div class="card">
            <div class="card-body">
                {{-- <h3 class="card-title">Gross</h3> --}}
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="">
                                {{-- <div class="text-right"> --}}
                                    <div class="row">
                                        <div class="col-md-5">
                                            <select name="dispatcher" class="form-control select2 me-1" id="dispatcher" required>
                                                <option value="">Choose dispatcher</option>
                                                @foreach ($dispatchers as $d)
                                                    <option value="{{ $d->id }}" <?php if (isset($_GET['dispatcher']) && $_GET['dispatcher'] == $d->id) {
                                                        echo 'selected';
                                                    } ?>>
                                                        {{ $d->dispatcher_name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-md-5">
                                            <select name="weeks" class="form-control select2 me-1" id="weeks">
                                                <option value="">Choose week</option>
                                                @forelse ($all_weeks as $d)
                                                    <option value="{{ $d->id }}" <?php if (isset($_GET['weeks']) && $_GET['weeks'] == $d->id) {
                                                        echo 'selected';
                                                    } ?>>
                                                        {{ $d->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-outline-primary">Filter</button>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                            </form>
                        </div>
                    </div>
                    <hr>

                    <h3 class="card-title">Gross</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Truck #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($_GET['dispatcher']))
                                    @foreach ($result as $dd)
                                        @php
                                            $truck = DB::table('truck')
                                                ->where('id', $dd['truck_id'])
                                                ->first();
                                            [$weekNumber, $totalWeeks, $year] = explode(' - ', @$dd['name']);
                                            $startDate = date('M d Y', strtotime("{$year}-W{$weekNumber}-1"));
                                            $endDate = date('M d Y', strtotime("{$year}-W{$weekNumber}-7"));
                                            $total_income = $total_income + $dd['net_amount'];
                                        @endphp
                                        @if($dd['net_amount'] != 0)
                                        <tr>
                                            <td>{{ $truck->truck_number }}</td>
                                            <td>{{ $startDate }}, {{ $endDate }}</td>
                                            <td>{{ number_format((float) $dd['net_amount'], 2, '.', '') }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($result as $dd)
                                        @php
                                            $truck = DB::table('truck')
                                                ->where('id', $dd->truck_id)
                                                ->first();
                                            [$weekNumber, $totalWeeks, $year] = explode(' - ', @$dd->name);
                                            $startDate = date('M d Y', strtotime("{$year}-W{$weekNumber}-1"));
                                            $endDate = date('M d Y', strtotime("{$year}-W{$weekNumber}-7"));
                                            $total_income = $total_income + $dd->net_amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $truck->truck_number }}</td>
                                            <td>{{ $startDate }}, {{ $endDate }}</td>
                                            <td>{{ number_format((float) $dd->net_amount, 2, '.', '') }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>

                    <h3 class="card-title mt-3">Expense</h3>
                    <div class="table-responsive expense">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Truck #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="repeater">
                                @if (isset($_GET['dispatcher']))
                                    @foreach ($result_expense as $e)
                                        <?php $total_expense = $total_expense + $e['amount']; ?>
                                        <tr>
                                            <td>
                                                <select class="form-select truck_id select2">
                                                    <option value="" disabled>Choose Truck</option>
                                                    @foreach ($all_trucks as $item)
                                                        <option value="{{ $item->id }}" <?php if ($e['truck_id'] == $item->id) {
                                                            echo 'selected';
                                                        } ?>>
                                                            {{ $item->truck_number }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="date" class="form-control date"
                                                    value="{{ $e['date'] }}"></td>
                                            <td><input type="text" class="form-control description"
                                                    placeholder="Description" value="{{ $e['description'] }}"></td>
                                            <td><input type="text" class="form-control amount" placeholder="Amount"
                                                    value="{{ number_format((float) $e['amount'], 2, '.', '') }}"></td>
                                            <td><a href="javascript:;" class="remove" class="text-danger"><i
                                                        class="fa fa-times"></i></a></td>
                                        </tr>
                                    @endforeach
                                @else
                                    @if (is_array($all_trucks_expense))
                                        @foreach ($all_trucks_expense as $e)
                                            <?php $total_expense = $total_expense + $e['amount']; ?>
                                            <tr>
                                                <td>
                                                    <select class="form-select truck_id select2" style="width: 100%;">
                                                        {{-- <option value="" disabled>Choose Truck</option> --}}
                                                        @foreach ($all_trucks as $item)
                                                            <option value="{{ $item->id }}" <?php if ($e->truck_id == $item->id) {
                                                                echo 'selected';
                                                            } ?>>
                                                                {{ $item->truck_number }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="date" class="form-control date"
                                                        value="{{ $e->date }}"></td>
                                                <td><input type="text" class="form-control description"
                                                        placeholder="Description" value="{{ $e->description }}"></td>
                                                <td><input type="text" class="form-control amount" placeholder="Amount"
                                                        value="{{ number_format((float) $e->amount, 2, '.', '') }}"></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endif
                            </tbody>
                        </table>
                        <button class="btn btn-primary mt-3 add-more btn-sm"><i class="fa fa-plus"></i> Add More</button>
                    </div>
                    <h3 class="card-title mt-3">Summary</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Gross</th>
                                    <th>Pay</th>
                                    <th>Expense</th>
                                    <th>Net</th>
                                </tr>
                            </thead>
                            @if (@$dispatcher_percentage)
                                @php
                                    $dispatcher_pay = ($dispatcher_percentage / 100) * $total_income;
                                    // $total_income = number_format((float) $dispatcher_pay + $total_income, 2, '.', '');
                                @endphp
                            @endif
                            <tbody>
                                <tr>
                                    <td>{{ $total_income }}</td>
                                    <td>{{ number_format((float) $dispatcher_pay, 2, '.', '') }}</td>
                                    <td>{{ $total_expense }}</td>
                                    <td class="net_total">
                                        {{ number_format((float) $dispatcher_pay - $total_expense, 2, '.', '') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" class="st-name" value="{{ $name }}">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-right">
                                    @if (@$dispatcher_id)
                                        <a class="btn btn-primary print ml-2 mr-2" style="float: right; margin-left: 10px;"
                                            href="{{ url('Dispatch/Statement/PDF') }}/{{ $dispatcher_id }}@if (request()->has('weeks')) ?week={{ request('weeks') }} @endif">Print</a>
                                    @else
                                        <a class="btn btn-primary print ml-2 mr-2" style="float: right; margin-left: 10px;"
                                            href="{{ url('Dispatch/Statement/PDF') }}">Print</a>
                                    @endif
                                    <button class="btn btn-primary print ml-2 mr-2 send-mail"
                                        data-dispatcher_id="{{ @$dispatcher_id }}" data-week="{{ $week_no }}"
                                        data-year="{{ $year_no }}"
                                        style="float: right; margin-left: 10px;">Send Email</button>
                                    <button class="btn btn-primary save-all" style="float: right">Save All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    {{-- {{ $categories->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
                </div>
            </div>
        </div>
    </div>

    </div>
    <div class="modal fade" id="addNewCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Add description here</h3>
                    </div>
                    <form method="POST" id="addNewCCForm" class="row g-3" action="">
                        @csrf
                        <input type="hidden" name="week_no" id="week_no">
                        <input type="hidden" name="year_no" id="year_no">
                        <div class="col-12">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Send Email</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('public') }}/assets/vendor/libs/select2/select2.js"></script>
    <script>
        $(".select2").select2({
            dropdownCss: {
                'z-index': 1000
            }
        });
        $('.table-responsive').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            // calculateSummary();
            // calculate();
        });
        $('.expense').on('click', '.add-more', function() {
            var html = `<tr>
                                        <td >
                                        <select class="form-select truck_id select2">
                                                @foreach ($all_trucks as $item)
                                                    <option value="{{ $item->id }}">{{ $item->truck_number }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                            <td><input type="date" class="form-control date" value="{{ date('Y-m-d') }}"></td>
                                            <td><input type="text" class="form-control description" placeholder="Description"></td>
                                            <td><input type="text" class="form-control amount" placeholder="Amount"></td>
                                            <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                                        </tr>`;
            $(this).parent().find('table').find('tbody').append(html);
            // calculateSummary();
            $(".select2").select2();
            // $(".select2-container--default .select2-dropdown").css('min-width', '500px');
        });

        $('.send-mail').on('click', function() {
            var dispatcher_id = $(this).data('dispatcher_id');
            var week = $(this).data('week');
            var year = $(this).data('year');
            if (dispatcher_id) {
                $('#addNewCCModal').modal('show');
                $('#week_no').val(week);
                    $('#year_no').val(year);
                $('#addNewCCForm').attr('action', '{{ url('sendEmailPDF/') }}/' + dispatcher_id +
                        '@if (request()->has('weeks'))?week={{ request('weeks') }} @endif');
            }
        })

        $('.save-all').on('click', function() {
            var net_total = $('.net_total').html();
            var st_name = $('.st-name').val();
            var data = {
                'expense': [],
                'name': st_name,
            };
            var expense = $('.expense').find('tr');
            expense.each(function() {
                var date = $(this).find('.date').val();
                var description = $(this).find('.description').val();
                var amount = $(this).find('.amount').val();
                var truck_id = $(this).find('.truck_id').val();

                data['expense'].push({
                    'date': date,
                    'description': description,
                    'amount': amount,
                    'truck_id': truck_id
                });
            });
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                type: 'POST',
                data: {
                    'data': data,
                    'net_total': net_total,
                },
                url: "{{ url('saveTruckExpense') }}",
                success: function(res) {
                    if (res) {
                        $('.alert-success').html('Data Saved Successfully').show();
                        window.location = '';
                    }
                }
            });
        });
    </script>
@endsection
