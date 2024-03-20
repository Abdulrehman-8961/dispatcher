<style>
    @media print{
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

    .table thead th, .table tbody td {
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

.table th, .table td {
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.table th {
    font-weight: 700;
}
*{
    font-family: Arial, Helvetica, sans-serif
}
</style>
    @php

        $info=DB::table('truck')
        ->select('truck.truck_number','drivers.driver_name')
        ->leftJoin('drivers','drivers.truck_id','=','truck.id')
        ->where('truck.id',request('id'))->first();
        // dd($info);
        if(request()->has('week') && request('week') != ""){
            $week_name = DB::table('truck_accounting')->where('id', request('week'))->first();
            if(!empty($week_name)){
                $name = $week_name->name;
            }else{
                $name = "";
            }
        }else{
            $currentDate = new DateTime();
            $weekNumber = $currentDate->format('W');
            $yearNumber = $currentDate->format('o');
            $firstDay = new DateTime($yearNumber . '-01-01');
            $lastDay = new DateTime($yearNumber . '-12-31');
            $totalWeeks = 52;
            $name = $weekNumber . ' - ' . $totalWeeks . ' - ' . $yearNumber;
        }
        $categories = DB::table('categories')->where('is_deleted', 0)->get();
        if(!empty($name)){
            $accounting = DB::table('truck_accounting')->where('truck_id', request('id'))->where('name', $name)->latest('created_on')->first();
            // dd($accounting);
            if(@$accounting->id){
                $income = DB::table('truck_income')->where('accounting_id', $accounting->id)->where('is_deleted', 0)->get();
                $expense = DB::table('truck_expense')->where('accounting_id', $accounting->id)->where('is_deleted', 0)->get();
            }else{
                $income = array();
                $expense = array();
            }
        }
// dd($expense);
        list($weekNumber, $totalWeeks, $year) = explode(" - ", $name);

        // Calculate the start date
        $startDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-1 -1 day"));
// dd($startDate);
        // Calculate the end date
        $endDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-7 -1 day"));
    @endphp
    <!-- Template Documents List (if needed) -->
    <center>
    <div>
            <img style="height: 100px;width:300px" src="{{asset('public/assets')}}/pdf_logo.png">
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
                    <h3 style="margin-bottom: 1px;margin-top:1px;font-size:20px">Statement #: {{$name}}</h3>
                    <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Driver's Name:</b> {{@$info->driver_name}}</p>
                    <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Truck #:</b> {{@$info->truck_number}}</p>
                    <p style="margin-bottom: 1px;margin-top:1px;font-size:13px;"><b>Statement Date:</b> {{date('F d Y',strtotime($startDate))}} - {{date('F d Y',strtotime($endDate))}}</p>
                </div>
        </div>
        <div>
                    @php
                        $week = DB::table('truck_accounting')->where('truck_id', request('id'))->get();
                        $totalincome = 0;
                    @endphp

            <table class="table" >
                <thead style="font-size: 12px;background:#dddddd">
                    <th style="width: 30%">DATE</th>
                    <th style="width: 40%">LOADS</th>
                    <th style="width: 30%">FREIGHT PAYMENT</th>
                </thead>
                <tbody style="font-size: 12px">
                    @foreach ($income as $i)
                    <tr>
                        <td>{{date('F d',strtotime($i->date))}}@if(isset($i->end_date)) - {{date('F d',strtotime(@$i->end_date))}}@endif</td>
                        <td>
                            {{$i->description}}
                            {{-- @foreach ($categories as $c)
                            @if($i->category == $c->id){{$c->name}} @endif
                        @endforeach --}}
                    </td>
                        <td><b>$</b><span style="float: right;margin-right:5px">{{ number_format((float)$i->amount, 2, '.', '') }}</span></td>
                    </tr>
                        @php
                            $totalincome = $totalincome + $i->amount;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
                <div style="width: 36.72%;float:right;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black">
                    <span style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL:</b></span>
                    <span style="float: right;">{{ number_format((float)$totalincome, 2, '.', '')}}</span>

                </div>
            </div>
            @php
                            $totalexpense = 0;
                        @endphp
            <table class="table" style="margin-top: 10px">
                <thead style="font-size: 12px;background:#dddddd">
                    <th style="width: 30%">DATE</th>
                    <th style="width: 40%">DEDUCTION</th>
                    <th style="30%">AMOUNT</th>
                </thead>
                <tbody style="font-size: 12px">
                    @foreach ($expense as $e)
                    <tr>
                        <td>{{date('F d',strtotime($e->date))}}@if(isset($e->end_date)) - {{date('F d',strtotime(@$e->end_date))}}@endif</td>
                        <td>{{$e->description}}</td>
                        <td><b>$</b><span style="float: right;margin-right:5px">{{number_format((float)$e->amount, 2, '.', '')}}</span></td>
                    </tr>
                    @php
                    $totalexpense = $totalexpense + $e->amount;
                @endphp
                @endforeach
                </tbody>
            </table>
                <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
                    <div style="width: 36.72%;float:right;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black">
                        <span style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL:</b></span>
                        <span style="float: right;">{{number_format((float)$totalexpense, 2, '.', '')}}</span>

                    </div>
                </div>

            <div style="width: 100% ;margin-top:-1px;margin-bottom:30px">
                    <div style="width: 41%;float:right;border:1px solid black;">
                        <span style="border-right:1px solid black;font-size:13px;padding-bottom:5px;padding-top:1.5px"><b>TOTAL NET:</b></span>
                        <span style="float: right;">{{ number_format((float)$totalincome-$totalexpense, 2, '.', '')}}</span>

                    </div>
                </div>
        </div>


<input type="hidden" id="dispatch_id" value="{{request('id')}}">
