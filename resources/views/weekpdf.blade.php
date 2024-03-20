<style>
    @media print {
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-6 {
            width: 50%;
            box-sizing: border-box;
        }
    }

    .text-center {
        text-align: center;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .col-md-6 {
        width: 50%;
        box-sizing: border-box;
    }

    .table thead th,
    .table tbody td {
        border: 1px solid #000;
    }

    .mt-0 {
        margin-top: 0;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }

    .table th {
        font-weight: 700;
    }

    * {
        font-family: Arial, Helvetica, sans-serif
    }
</style>
@php
    use Carbon\Carbon;
    $categories = DB::table('categories')
        ->where('is_deleted', 0)
        ->get();
    $dispatcher = DB::table('dispatchers')
        ->where('id', $dispatcher_id)
        ->first();

    if (request()->has('week') && request('week') != '') {
        $week_name = DB::table('truck_accounting')
            ->where('id', request('week'))
            ->first();
        if (!empty($week_name)) {
            $name = $week_name->name;
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
    }
    $dispatcherAmount = $dispatcher->salary;
    $numbers_string = $dispatcher->truck_id;
    $truck_ids = explode(',', $numbers_string);
    $numbers_array = array_map('intval', explode(',', $numbers_string));
    [$weekNumber, $totalWeeks, $year] = explode(' - ', $name);

    // Calculate the start date
    $startDate = date('F d Y', strtotime("{$year}-W{$weekNumber}-1"));

    // Calculate the end date
    $endDate = date('F d Y', strtotime("{$year}-W{$weekNumber}-7"));
@endphp
<!-- Template Documents List (if needed) -->
<center>
    <div>
        <img style="height: 100px;width:300px" src="{{ asset('public/assets') }}/pdf_logo.png">
    </div>
</center>
<div style="width: 100%;height:100px">
    <div style="float:left; width: 40%">
        <h3 style="margin-bottom: 1px;margin-top:1px;font-size:20px">American Trans LLC</h3>
        <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Address:</b> 2100 N HWY 360 Unit 300A</p>
        <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Phone:</b> (817)-400-0989</p>
        <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Email:</b> americantrans18@gmail.com</p>
    </div>

    <div style="float:right; width: 40%">
        <h3 style="margin-bottom: 1px;margin-top:1px;font-size:20px">Statement #: {{ $name }}</h3>
        <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Dispatcher:</b>
            {{ $dispatcher->dispatcher_name }}</p>
        <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Statement Date:</b> {{ $startDate }} -
            {{ $endDate }}</p>
    </div>
</div>
<div>
    @php

        $all_trucks_income = DB::table('truck_accounting')
            ->leftjoin('truck_income', 'truck_accounting.id', '=', 'truck_income.accounting_id')
            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id', DB::raw('COALESCE(SUM(truck_income.amount), 0) as income_amount'))
            ->where('truck_accounting.name', $name)
            ->whereIn('truck_accounting.truck_id', $numbers_array)
            ->where('truck_accounting.is_deleted', 0)
            ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_income.accounting_id')
            ->get();

        $all_trucks_expense_ = DB::table('truck_accounting')
            ->leftjoin('truck_expense', 'truck_accounting.id', '=', 'truck_expense.accounting_id')
            ->select('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id', DB::raw('COALESCE(SUM(truck_expense.amount), 0) as expense_amount'))
            ->where('truck_accounting.name', $name)
            ->whereIn('truck_accounting.truck_id', $numbers_array)
            ->where('truck_accounting.is_deleted', 0)
            ->groupBy('truck_accounting.id', 'truck_accounting.truck_id', 'truck_accounting.name', 'truck_expense.accounting_id')
            ->get();

        // Combine the results in PHP
        $result = $all_trucks_income->map(function ($income) use ($all_trucks_expense_) {
            $expense = $all_trucks_expense_->where('id', $income->id)->first();
            $income->net_amount = $income->income_amount;
            return $income;
        });

        $all_trucks_expense = DB::table('extra_truck_expense')
            ->where('is_deleted', 0)
            ->whereIn('truck_id', $numbers_array)

            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();
        $totalincome = 0;
        $totalexpense = 0;
    @endphp

    <table class="table">
        <thead style="font-size: 12px;background:#dddddd">
            <th style="width: 20%">Truck #</th>
            <th style="width: 40%">DATE</th>
            <th style="width: 40%">FREIGHT PAYMENT</th>
        </thead>
        <tbody style="font-size: 12px">
            @foreach ($all_trucks_income as $dd)
                @php
                    $truck = DB::table('truck')
                        ->where('id', $dd->truck_id)
                        ->first();
                    // dd($truck);
                    [$weekNumber, $totalWeeks, $year] = explode(' - ', @$dd->name);
                    $startDate = date('F d Y', strtotime("{$year}-W{$weekNumber}-1"));
                    $endDate = date('F d Y', strtotime("{$year}-W{$weekNumber}-7"));
                    $totalincome = $totalincome + $dd->net_amount;
                @endphp
                <tr>
                    <td>{{ $truck->truck_number }}</td>
                    <td>{{ $startDate }} - {{ $endDate }}</td>
                    <td><b>$</b><span
                            style="float: right;margin-right:5px">{{ number_format((float) $dd->net_amount, 2, '.', '') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @php
        $dispatcherSalary = 0;
        if ($dispatcherAmount) {
            $dispatcherSalary = ($dispatcherAmount / 100) * $totalincome;
            $formatedNumber = number_format((float) $dispatcherSalary, 2, '.', '');
        }
    @endphp
    <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
        <div
            style="width: 36.72%;float:right;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black">
            <span
                style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL:</b></span>
            <span style="float: right;">{{ number_format((float) $totalincome, 2, '.', '') }}</span>

        </div>
    </div>
    <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
        <div style="width: 41%;float:right;border:1px solid black;">
            <span style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>DISPATCHER
                    PAY:</b></span>
            <span style="float: right;">{{ $formatedNumber }}</span>

        </div>
    </div>

    <table class="table" style="margin-top: 10px">
        <thead style="font-size: 12px;background:#dddddd">
            <th style="width: 15%">Truck #</th>
            <th style="width: 20%">DATE</th>
            <th style="width: 35%">DEDUCTION</th>
            <th style="30%">AMOUNT</th>
        </thead>
        <tbody style="font-size: 12px">
            @foreach ($all_trucks_expense as $e)
                <?php
                $totalexpense = $totalexpense + $e->amount;
                ?>
                <tr>
                    @php
                        $truck_no = DB::table('truck')
                            ->where('id', $e->truck_id)
                            ->select('truck_number')
                            ->first();
                    @endphp
                    <td>{{ $truck_no->truck_number }}</td>
                    <td>{{ date('F d Y', strtotime($e->date)) }}</td>
                    <td>{{ $e->description }}</td>
                    <td><b>$</b><span
                            style="float: right;margin-right:5px">{{ number_format((float) $e->amount, 2, '.', '') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
        <div
            style="width: 36.72%;float:right;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black">
            <span
                style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL:</b></span>
            <span style="float: right;">{{ number_format((float) $totalexpense, 2, '.', '') }}</span>

        </div>
    </div>

    <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
        <div style="width: 41%;float:right;border:1px solid black;">
            <span style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL
                    NET:</b></span>
            <span style="float: right;">{{ number_format((float) $formatedNumber - $totalexpense, 2, '.', '') }}</span>

        </div>
    </div>
</div>


<input type="hidden" id="dispatch_id" value="{{ request('id') }}">
