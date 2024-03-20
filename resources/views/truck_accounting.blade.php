@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Truck /</span> Accounting</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">
            <div class="row">
                <div class="col-md-6">
                    Truck Accounting
                </div>
            </div>
        </h5>
        @php
            $dispatcher = DB::table('dispatchers')->where('is_deleted', 0)->get();
        @endphp
        <div class="container pb-4">
            @php
                $dispatched = DB::table('truck_dispatch as td')
                ->join('truck as t', 't.id', '=', 'td.truck_id')
                ->join('dispatchers as d', 'd.id', '=', 'td.dispatcher_id')
                ->where('t.is_deleted', 0)->where('d.is_deleted', 0)->where('td.is_deleted', 0)
                ->select('td.id as dispatched_id','t.truck_number', 't.vin', 't.year', 't.make', 't.model', 't.plate_number', 'd.dispatcher_name', 'td.created_on')->get();
                $i = 1;
                // dd($dispatched);
            @endphp
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dispatcher</th>
                                    <th>Truck Number</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="dis-truck">
                                @foreach ($dispatched as $d)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$d->dispatcher_name}}</td>
                                    <td>{{$d->truck_number}}</td>
                                    <td>{{$d->created_on}}</td>
                                    <td><a href="{{url('Truck/Accounting/'.$d->dispatched_id)}}"><i class="ti ti-calculator"></i></a></td>
                                </tr>
                                <?php $i++ ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endsection
