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
        padding: 8px;
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
    margin-bottom: 20px;
    border-collapse: collapse;
    border-spacing: 0;
}

.table th, .table td {
    padding: 8px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.table th {
    font-weight: 700;
}
</style>
    @php
        $info = DB::table('truck_dispatch as td')->leftjoin('truck as t', 't.id', '=', 'td.truck_id')->leftjoin('drivers as d', 'd.truck_number', '=', 't.truck_number')->leftjoin('owners as o', 'o.id', '=', 't.company_id')->select('d.driver_name','td.created_on', 't.truck_number', 't.truck_address', 'o.email', 'o.phone', 'o.owner_name', 'o.company_name')->where('td.is_deleted', 0)->where('td.id', request('id'))->first();
      
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
            $totalWeeks = ceil(($lastDay->format('z') + 1) / 7);
            $name = $weekNumber . ' - ' . $totalWeeks . ' - ' . $yearNumber;
        }
        $categories = DB::table('categories')->where('is_deleted', 0)->get();
        if(!empty($name)){
            $accounting = DB::table('truck_accounting')->where('dispatch_id', request('id'))->where('name', $name)->first();
            if(@$accounting->id){
                $income = DB::table('truck_income')->where('accounting_id', $accounting->id)->where('is_deleted', 0)->get();
                $expense = DB::table('truck_expense')->where('accounting_id', $accounting->id)->where('is_deleted', 0)->get();
            }else{
                $income = array();
                $expense = array();
            }
        }

        list($weekNumber, $totalWeeks, $year) = explode(" - ", $name);

        // Calculate the start date
        $startDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-1"));

        // Calculate the end date
        $endDate = date("d M Y", strtotime("{$year}-W{$weekNumber}-7"));
    @endphp
    <!-- Template Documents List (if needed) -->
        <div style="width: 100%;">
            <center><img src="{{asset('public/assets')}}/logo.png"></center>
        </div>
        <div style="display:flex; flex-wrap: wrap;">
            <div style="width: 40%">
                <h3>American Trans LLC</h3>
                <p><b>Address:</b> 62300 Bahawalnagar, Punjab, Pakistan</p>
                <p><b>Phone:</b> (92) 301 793 4476</p>
                <p><b>Email:</b> info@atllc.com</p>
            </div>
            <div style="width: 40%">
                <h3>Statement #: {{$name}}</h3>
                <p><b>Driver's Name:</b> {{$info->driver_name}}</p>
                <p><b>Truck #:</b> {{$info->truck_number}}</p>
                <p><b>Statement Date:</b> {{$startDate}}, {{$endDate}}</p>
            </div>
        </div>
    <div class="row">

        @if(!empty($name))
        <div class="col-md-12">
            <div>
                <div>
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
                                    <td>{{$info->owner_name}}</td>
                                    <td>{{$info->email}}</td>
                                    <td>{{$info->phone}}</td>
                                    <td>{{$info->truck_number}}</td>
                                    <td>{{$info->truck_address}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @php
                        $week = DB::table('truck_accounting')->where('dispatch_id', request('id'))->get();
                    @endphp
                    <div class="row mt-5">
                        @php
                            $totalincome = 0;
                        @endphp
                        <div class="col-md-12">
                            <h3>Income</h3>
                            <div class="table-responsive income">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Description</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">8%</th>
                                            <th class="text-center">3%</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="repeater">
                                        @if(count($income) > 0)
                                            @foreach ($income as $i)
                                            <tr>
                                                <td><p class="text-center">{{$i->date}}</p></td>
                                                <td>
                                                        @foreach ($categories as $c)
                                                            @if($i->category == $c->id) <p class="text-center">{{$c->name}}</p> @endif
                                                        @endforeach
                                                </td>
                                                <td><p class="text-center">{{$i->description}}</p></td>
                                                <td><p class="text-center">{{$i->amount}}</p></td>
                                                <td><p class="text-center">@if($i->percent == "8%") <i class="fa fa-check-double"></i> @else <i class="fa fa-times-circle"></i> @endif</p></td>
                                                <td><p class="text-center">@if($i->percent == "3%") <i class="fa fa-check-double"></i> @else <i class="fa fa-times-circle"></i> @endif</p></td>
                                                <td></td>
                                                <input type="hidden" class="percent_val" value="{{$i->percent}}">
                                            </tr>
                                            @php
                                                $totalincome = $totalincome + $i->amount;
                                            @endphp
                                            @endforeach
                                        @else
                                        <tr>
                                            <td><p class="text-center">N/A</p></td>
                                            <td>
                                                <p class="text-center">N/A</p>
                                            </td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                {{-- <button class="btn btn-primary mt-3 add-more btn-sm"><i class="fa fa-plus"></i> Add More</button> --}}
                            </div>
                        </div>
                        @php
                            $totalexpense = 0;
                        @endphp
                        <div class="col-md-12 mt-3">
                            <hr>
                            <h3>Expense</h3>
                            <div class="table-responsive expense">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Description</th>
                                            <th class="text-center">Amount</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="repeater">
                                        @if(count($expense) > 0)
                                        @foreach ($expense as $e)
                                        <tr @if($e->description == "8%") class="eight" @elseif($e->description == "3%") class="three" @endif>
                                            <td><p class="text-center">{{$e->date}}</p></td>
                                            <td><p class="text-center">{{$e->description}}</p></td>
                                            <td><p class="text-center">{{$e->amount}}</p></td>
                                            <td></td>
                                        </tr>
                                        @php
                                            $totalexpense = $totalexpense + $e->amount;
                                        @endphp
                                        @endforeach
                                        @else
                                        <tr>
                                            <td><p class="text-center">N/A</p></td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td><p class="text-center">N/A</p></td>
                                            <td></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                {{-- <button class="btn btn-primary mt-3 add-more btn-sm"><i class="fa fa-plus"></i> Add More</button> --}}
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <hr>
                            <h3>Summary</h3>
                            <div class="table-responsive summry">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Total Income</th>
                                            <th class="text-center">Total Expense</th>
                                            <th class="text-center">Net Incom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><p class="text-center">{{$totalincome}}</p></td>
                                            <td><p class="text-center">{{$totalexpense}}</p></td>
                                            <td><p class="text-center">{{$totalincome-$totalexpense}}</p></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                <a href="{{url('/')}}" class="btn btn-primary"><i class="fa fa-arrow-circle-left"></i> &nbsp; Return to Dashboard</a>
            </div>
        </div>
        @endif
    </div>
</div>
<input type="hidden" id="dispatch_id" value="{{request('id')}}">
