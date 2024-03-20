@extends('components/header')
@section('main')
<link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/flatpickr/flatpickr.css" />
<link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Truck Accounting</h4>
        @php
            dd('');
            $week_no = '';
            $year_no = '';
            $info = DB::table('truck as t')
                // ->join('truck as t', 't.id', '=', 'td.truck_id')
                ->join('owners as o', 'o.id', '=', 't.company_id')
                ->select('t.created_on', 't.truck_number', 't.truck_address', 'o.email', 'o.phone', 'o.owner_name', 'o.company_name')
                ->where('t.is_deleted', 0)
                ->where('t.id', request('id'))
                ->first();
            // dd($info);
            if (request()->has('week') && request('week') != '') {
                $week_name = DB::table('truck_accounting')
                    ->where('id', request('week'))
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
            $categories = DB::table('categories')
                ->where('is_deleted', 0)
                ->get();
            if (!empty($name)) {
                $accounting = DB::table('truck_accounting')
                    ->where('truck_id', request('id'))
                    ->where('is_deleted', 0)
                    ->where('name', $name)
                    ->first();
                if (@$accounting->id) {
                    $income = DB::table('truck_income')
                        ->where('accounting_id', $accounting->id)
                        ->where('is_deleted', 0)
                        ->get();
                    $expense = DB::table('truck_expense')
                        ->where('accounting_id', $accounting->id)
                        ->where('is_deleted', 0)
                        ->get();
                } else {
                    $income = [];
                    $expense = [];
                }
            }
            $all_trucks = DB::table('truck')
                ->where('is_deleted', 0)
                ->get();
        @endphp
        <!-- Template Documents List (if needed) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Trucks</label>
                                <select name="dispatcher" class="form-control select2" id="dispatcher_account">
                                    {{-- <option value="">Choose Dispatcher</option> --}}
                                    @foreach ($all_trucks as $t)
                                        <option value="{{ $t->id }}" <?php if (request('id') == $t->id) {
                                            echo 'selected';
                                        } ?>>{{ $t->truck_number }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error"></span>
                            </div>
                        </div>
                    </div>
                    @if (!empty($name))
                        <div class="card-body">
                            <h5>Truck Info</h5>
                            <p class="alert alert-success" style="display: none;"></p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Owner Name</th>
                                            <th>Owner Email</th>
                                            <th>Owner Phone</th>
                                            <th>Truck #</th>
                                            <th>Truck Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ @$info->owner_name }}</td>
                                            <td>{{ @$info->email }}</td>
                                            <td>{{ @$info->phone }}</td>
                                            <td>{{ @$info->truck_number }}</td>
                                            <td>{{ @$info->truck_address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @php
                                $week = DB::table('truck_accounting')
                                    ->where('truck_id', request('id'))
                                    ->groupBy('name')
                                    ->get();
                            @endphp
                            <div class="row mt-5">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <h5 class="mb-0">Statement Name: &nbsp;</h5>
                                        <p class="mt-0 st-name"><input type="text" name="statement_name" id="statement_name" class="from-control" placeholder="00-00-0000"></p>
                                    </div>
                                </div>
                                <?php $settings = DB::Table('settings')->first(); ?>
                                <div class="col-md-12">
                                    <h3>Income</h3>
                                    <div class="table-responsive income">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    {{-- <th>Category</th> --}}
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>{{ $settings->value_1 }}%</th>
                                                    <th>{{ $settings->value_2 }}%</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="repeater">
                                                @if (count($income) > 0)
                                                    @foreach ($income as $i)
                                                        <tr>
                                                            <td>
                                                                <div class="input-group input-daterange bs-datepicker-daterange2"
                                                                    id="">
                                                                    <input type="text" id="dateRangePicker"
                                                                        value="{{ $i->date }}"
                                                                        placeholder="MM/DD/YYYY"
                                                                        class="form-control startDate" />
                                                                    <span class="input-group-text">to</span>
                                                                    <input type="text" placeholder="MM/DD/YYYY"
                                                                        class="form-control endDate" />
                                                                </div>
                                                                {{-- <input type="date" class="form-control date"
                                                                    value="{{ $i->date }}"></td> --}}
                                                                {{-- <td>
                                                                <select name="category" class="form-control category">
                                                                    <option value="">Choose Category</option>
                                                                    @foreach ($categories as $c)
                                                                        <option value="{{ $c->id }}"
                                                                            @if ($i->category == $c->id) selected @endif>
                                                                            {{ $c->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td> --}}
                                                            <td><input type="text" class="form-control description"
                                                                    placeholder="Description"
                                                                    value="{{ $i->description }}"></td>
                                                            <td><input type="text" class="form-control amount"
                                                                    placeholder="Amount"
                                                                    value="{{ number_format((float) $i->amount, 2, '.', '') }}">
                                                            </td>
                                                            <td><input type="checkbox" class="is_eight percent"
                                                                    data-value="{{ $settings->value_1 }}%" value="1"
                                                                    @if ($i->percent == $settings->value_1 . '%') checked @endif></td>
                                                            <td><input type="checkbox" class="is_three percent"
                                                                    data-value="{{ $settings->value_2 }}%" value="1"
                                                                    @if ($i->percent == $settings->value_2 . '%') checked @endif></td>
                                                            <td><a href="javascript:;" class="remove" class="text-danger"><i
                                                                        class="fa fa-times"></i></a></td>
                                                            <input type="hidden" class="percent_val"
                                                                value="{{ $i->percent }}">
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>
                                                            <div class="input-group input-daterange bs-datepicker-daterange2"
                                                                id="">
                                                                <input type="text" id="dateRangePicker"
                                                                    placeholder="MM/DD/YYYY"
                                                                    class="form-control startDate" />
                                                                <span class="input-group-text">to</span>
                                                                <input type="text" placeholder="MM/DD/YYYY"
                                                                    class="form-control endDate" />
                                                            </div>
                                                            {{-- <input type="date" class="form-control date"
                                                                value="{{ date('Y-m-d') }}"></td> --}}
                                                            {{-- <td>
                                                            <select name="category" class="form-control category">
                                                                <option value="">Choose Category</option>
                                                                @foreach ($categories as $c)
                                                                    <option value="{{ $c->id }}">
                                                                        {{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td> --}}
                                                        <td><input type="text" class="form-control description"
                                                                placeholder="Description"></td>
                                                        <td><input type="text" class="form-control amount"
                                                                placeholder="Amount"></td>
                                                        <td><input type="checkbox" class="is_eight percent"
                                                                data-value="{{ $settings->value_1 }}%" value="1">
                                                        </td>
                                                        <td><input type="checkbox" class="is_three percent"
                                                                data-value="{{ $settings->value_2 }}%" value="1">
                                                        </td>
                                                        <td><a href="javascript:;" class="remove" class="text-danger"><i
                                                                    class="fa fa-times"></i></a></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <button class="btn btn-primary mt-3 add-more btn-sm"><i class="fa fa-plus"></i>
                                            Add
                                            More</button>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <hr>
                                    <h3>Expense</h3>
                                    <div class="table-responsive expense">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="repeater">
                                                @if (count($expense) > 0)
                                                    @foreach ($expense as $e)
                                                        <tr
                                                            @if ($e->description == $settings->value_1 . '%') class="eight" @elseif($e->description == $settings->value_2 . '%') class="three" @endif>
                                                            <td>
                                                                <div class="input-group input-daterange bs-datepicker-daterange"
                                                                    id="">
                                                                    <input type="text" id="dateRangePicker"
                                                                        value="{{ $e->date }}"
                                                                        placeholder="MM/DD/YYYY"
                                                                        class="form-control startDate" />
                                                                    <span class="input-group-text">to</span>
                                                                    <input type="text" placeholder="MM/DD/YYYY"
                                                                        class="form-control endDate" />
                                                                </div>
                                                                {{-- <input type="date" class="form-control date"
                                                                    value="{{ $e->date }}"> --}}
                                                            </td>
                                                            <td>
                                                                <select name="category" class="form-control category">
                                                                    <option value="">Choose Category</option>
                                                                    @foreach ($categories as $c)
                                                                        <option value="{{ $c->id }}"
                                                                            @if ($e->category == $c->id) selected @endif>
                                                                            {{ $c->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control description"
                                                                    placeholder="Description"
                                                                    value="{{ $e->description }}"></td>
                                                            <td><input type="text" class="form-control amount"
                                                                    placeholder="Amount"
                                                                    value="{{ number_format((float) $e->amount, 2, '.', '') }}">
                                                            </td>
                                                            <td><a href="javascript:;" class="remove"
                                                                    class="text-danger"><i class="fa fa-times"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>
                                                            <div class="input-group input-daterange bs-datepicker-daterange"
                                                                id="">
                                                                <input type="text" id="dateRangePicker"
                                                                    placeholder="MM/DD/YYYY"
                                                                    class="form-control startDate" />
                                                                <span class="input-group-text">to</span>
                                                                <input type="text" placeholder="MM/DD/YYYY"
                                                                    class="form-control endDate" />
                                                            </div>
                                                            {{-- <input type="date" class="form-control date"
                                                                value="{{ date('Y-m-d') }}"> --}}
                                                        </td>
                                                        <td>
                                                            <select name="category" class="form-control category">
                                                                <option value="">Choose Category</option>
                                                                @foreach ($categories as $c)
                                                                    <option value="{{ $c->id }}">
                                                                        {{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control description"
                                                                placeholder="Description"></td>
                                                        <td><input type="text" class="form-control amount"
                                                                placeholder="Amount"></td>
                                                        <td><a href="javascript:;" class="remove" class="text-danger"><i
                                                                    class="fa fa-times"></i></a></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <button class="btn btn-primary mt-3 add-more btn-sm"><i class="fa fa-plus"></i>
                                            Add More</button>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <hr>
                                    <h3>Summary</h3>
                                    <div class="table-responsive summry">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Total Income</th>
                                                    <th>Total Expense</th>
                                                    <th>Net Income</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group text-right">
                                        <a class="btn btn-primary print ml-2 mr-2"
                                            style="float: right; margin-left: 10px;"
                                            href="{{ url('Truck/Accounting/PDF') }}/{{ request('id') }}@if (request()->has('week')) ?week={{ request('week') }} @endif">Print</a>
                                        <button class="btn btn-primary print ml-2 mr-2 send-mail"
                                            data-id="{{ request('id') }}" data-week="{{ $week_no }}"
                                            data-year="{{ $year_no }}" style="float: right; margin-left: 10px;">Send
                                            Email</button>
                                        <button class="btn btn-primary save-all" style="float: right">Save All</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-12">
                            <h3 class="text-center">
                                Noting to display
                            </h3>
                            <div class="text-center">
                                <a href="{{ url('/') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-circle-left"></i> &nbsp; Return to Dashboard</a>
                            </div>
                        </div>
                    @endif
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
                        <h3 class="mb-2">Add email here</h3>
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
    <input type="hidden" id="truck_id" value="{{ request('id') }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{--  --}}

    <script src="{{ asset('public') }}/assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('public') }}/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    {{-- <script src="{{ asset('public') }}/assets/js/forms-pickers.js"></script> --}}
    <script>
        $(document).ready(function() {
            $(".select2").select2();
            $(".bs-datepicker-daterange2").datepicker({
      todayHighlight: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
            $(".bs-datepicker-daterange").datepicker({
      todayHighlight: true,
      orientation: isRtl ? 'auto right' : 'auto left'
    });
            $('#dispatcher_account').on('change', function() {
                var id = $(this).val();
                window.location = "{{ url('Truck/Accounting/previous') }}/" + id
            })
            calculate();
            calculateSummary();

            var categories = "{{ $categories }}";
            var json = categories.replace(/&quot;/g, '"');
            var cats = JSON.parse(json);
            var catHTML = '';
            cats.forEach(c => {
                catHTML += `<option value="` + c.id + `">` + c.name + `</option>`;
            });
            $('.income').on('click', '.add-more', function() {
                var lastRow = $('.income tr:last');
                // var date = lastRow.find('.date').val();
                // var percent = lastRow.find('.percent_val').val();
                var html = `<tr>
                                            <td><div class="input-group input-daterange" id="bs-datepicker-daterange">
                                                                <input type="text" id="dateRangePicker" placeholder="MM/DD/YYYY" class="form-control startDate" />
                                                                <span class="input-group-text">to</span>
                                                                <input type="text" placeholder="MM/DD/YYYY" class="form-control endDate" />
                                                              </div></td>
                                            <td><input type="text" class="form-control description" placeholder="Description"></td>
                                            <td><input type="text" class="form-control amount" placeholder="Amount"></td>
                                            <td><input type="checkbox" class="is_eight percent" data-value="{{ $settings->value_1 }}%" value="1"></td>
                                            <td><input type="checkbox" class="is_three percent" data-value="{{ $settings->value_2 }}%" value="1"></td>
                                            <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                                        </tr>`;
                $(this).parent().find('table').find('tbody').append(html);

                calculate();
            });

            $('.expense').on('click', '.add-more', function() {
                var html = `<tr>
                                            <td><div class="input-group input-daterange" id="bs-datepicker-daterange">
                                                                <input type="text" id="dateRangePicker" placeholder="MM/DD/YYYY" class="form-control startDate" />
                                                                <span class="input-group-text">to</span>
                                                                <input type="text" placeholder="MM/DD/YYYY" class="form-control endDate" />
                                                              </div></td>
                                            <td>
                                                <select name="category" class="form-control category">
                                                    <option value="">Choose Category</option>`;
                @foreach ($categories as $c)
                    html += `<option value="{{ $c->id }}">{{ $c->name }}</option>`;
                @endforeach

                html += `</select>
                                            </td>
                                            <td><input type="text" class="form-control description" placeholder="Description"></td>
                                            <td><input type="text" class="form-control amount" placeholder="Amount"></td>
                                            <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                                        </tr>`;
                $(this).parent().find('table').find('tbody').append(html);
                calculateSummary();
            });

            $('.table-responsive').on('click', '.remove', function() {
                $(this).parent().parent().remove();
                calculateSummary();
                calculate();
            });

            $('.income').on('click', '.percent', function() {
                var element = $(this);
                var tr = $(this).parent().parent();
                if ($(this).is(':checked')) {
                    var val = $(this).attr('data-value');
                    if ($(tr).find('.percent_val').length > 0) {
                        $(tr).find('.percent_val').val(val);
                    } else {
                        $(tr).append(`<input type="hidden" class="percent_val" value="` + val + `" />`);
                    }
                    var per = $(tr).find('.percent');
                    // per.each(function() {
                    //     $(this).prop('checked', false);
                    // });
                    // $(element).prop('checked', true);
                }
                calculate();
            });

            function calculate() {
                var tr = $('.income').find('tbody').find('tr');
                var eight = 0;
                var three = 0;
                tr.each(function() {
                    var date = $(this).find('.date').val();
                    var amount = $(this).find('.amount').val();
                    var percent = $(this).find('.percent_val').val();
                    var perNumber = parseInt(percent);
                    if ((amount == "" || amount == null) || !perNumber) {
                        return false;
                    }
                    var totalPercentAmount = (perNumber / 100) * amount;
                    if (perNumber == {{ $settings->value_1 }}) {
                        eight = eight + totalPercentAmount;
                    } else {
                        three = three + totalPercentAmount;
                    }
                    if (perNumber == {{ $settings->value_1 }}) {
                        var eightHTML = `<tr class="eight">
                    <td><input type="date" class="form-control date" value="{{ date('Y-m-d') }}"></td>
                    <td>
                        <select name="category" class="form-control category">
                            <option value="">Choose Category</option>`;
                        @foreach ($categories as $c)
                            eightHTML +=
                                `<option value="{{ $c->id }}">{{ $c->name }}</option>`;
                        @endforeach

                        eightHTML += `</select>
                    </td>
                    <td><input type="text" class="form-control description" placeholder="Description" value="` +
                            percent + `"></td>
                    <td><input type="text" class="form-control amount" placeholder="Amount" value="` + eight + `"></td>
                    <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                    </tr>`;
                        if ($('.expense').find('.eight').length > 0) {
                            $('.expense').find('.eight').find('.amount').val(parseFloat(eight).toFixed(2));
                        } else {
                            $('.expense').find('tbody').append(eightHTML);
                        }
                        // console.log(eight);
                    } else {
                        var threeHTML = `<tr class="three">
                    <td><input type="date" class="form-control date" value="{{ date('Y-m-d') }}"></td>
                    <td>
                        <select name="category" class="form-control category">
                            <option value="">Choose Category</option>`;
                        @foreach ($categories as $c)
                            threeHTML +=
                                `<option value="{{ $c->id }}">{{ $c->name }}</option>`;
                        @endforeach

                        threeHTML += `</select>
                    </td>
                    <td><input type="text" class="form-control description" placeholder="Description" value="` +
                            percent + `"></td>
                    <td><input type="text" class="form-control amount" placeholder="Amount" value="` + three + `"></td>
                    <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                    </tr>`;
                        if ($('.expense').find('.three').length > 0) {
                            $('.expense').find('.three').find('.amount').val(three);
                        } else {
                            $('.expense').find('tbody').append(threeHTML);
                        }
                    }

                });
                calculateSummary();
            }

            $('.expense').on('keyup', '.amount', function() {
                calculate();
                calculateSummary();
            });

            $('.income').on('keyup', '.amount', function() {
                calculate();
                calculateSummary();
            });

            function calculateSummary() {
                var total_income = 0;
                var total_expense = 0;
                var exp = $('.expense').find('.amount');
                exp.each(function() {
                    if ($(this).val() != "" && $(this).val() != null) {
                        total_expense = parseFloat(total_expense) + parseFloat($(this).val());
                        // console.log($(this).val())
                    }
                });

                var income = $('.income').find('.amount');
                income.each(function() {
                    if ($(this).val() != "" && $(this).val() != null) {
                        total_income = parseFloat(total_income) + parseFloat($(this).val());
                        // console.log($(this).val())
                    }
                });

                var summaryHTML = `<tr>
        <td>` + total_income + `</td>
        <td>` + total_expense + `</td>
        <td>` + (total_income - total_expense) + `</td>
    </tr>`;

                $('.summry').find('tbody').html(summaryHTML);
            }

            $('.send-mail').on('click', function() {
                var id = $(this).data('id');
                var week = $(this).data('week');
                var year = $(this).data('year');
                if (id) {
                    $('#addNewCCModal').modal('show');
                    $('#week_no').val(week);
                    $('#year_no').val(year);
                    $('#addNewCCForm').attr('action', '{{ url('sendTruckAccountingPDF/') }}/' + id +
                        '@if (request()->has('week'))?week={{ request('week') }} @endif'
                        );
                }
            })

            $('.save-all').on('click', function() {
                var st_name = $('.st-name').html();
                var truck_id = $('#truck_id').val();
                var income = $('.income').find('tr');
                var data = {
                    'income': [],
                    'expense': [],
                    'name': st_name,
                    'id': truck_id
                };

                income.each(function() {
                    var startDate = $(this).find('.startDate').val();
                    var endDate = $(this).find('.endDate').val();
                    var description = $(this).find('.description').val();
                    var amount = $(this).find('.amount').val();
                    var percent = $(this).find('.percent_val').val();

                    data['income'].push({
                        'startdate': startDate,
                        'enddate': endDate,
                        'description': description,
                        'amount': amount,
                        'percent': percent
                    });
                });

                var expense = $('.expense').find('tr');
                expense.each(function() {
                    var startDate = $(this).find('.startDate').val();
                    var endDate = $(this).find('.endDate').val();
                    var category = $(this).find('.category').val();
                    var description = $(this).find('.description').val();
                    var amount = $(this).find('.amount').val();

                    data['expense'].push({
                        'startdate': startDate,
                        'enddate': endDate,
                        'category': category,
                        'description': description,
                        'amount': amount
                    });
                });
                console.log(data['income']);
                console.log(data['expense']);


                $.ajax({
                    type: 'POST',
                    data: {
                        'data': data
                    },
                    url: "{{ url('saveTruckAccountInfo') }}",
                    success: function(res) {
                        if (res) {
                            $('.alert-success').html('Data Saved Successfully').show();
                            window.location = '';
                        }
                    }
                });
            });


        });
    </script>
@endsection
