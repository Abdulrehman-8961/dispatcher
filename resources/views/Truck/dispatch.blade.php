@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Truck /</span> Dispatch</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">
            <div class="row">
                <div class="col-md-6">
                    Dispatch Trucks
                </div>
            </div>
        </h5>
        @php
            $dispatcher = DB::table('dispatchers')->where('is_deleted', 0)->get();
        @endphp
        <div class="container pb-4">
            <form action="{{ url('DispatchTruck') }}" method="POST" id="saveDispatcherForm">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <p class="alert alert-success d-none"></p>
                        <p class="alert alert-danger d-none"></p>
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Disptacher</label>
                            <select name="dispatcher" class="form-control" id="dispatcher">
                                <option value="">Choose Dispatcher</option>
                                @foreach ($dispatcher as $d)
                                <option value="{{$d->id}}" <?php if(isset($_GET['dispatcher']) && $_GET['dispatcher']==$d->id ) echo 'selected' ?>
                                    >{{$d->dispatcher_name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error"></span>
                        </div>
                    </div>
                    <div class="col-md-4 d-none">
                        <div class="form-group">
                            <label for="">Trucks</label>
                            <select name="trucks" class="form-control" id="truck">
                                <option value="">Choose Truck</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="" style="color: transparent">.</label>
                        <div class="form-group d-flex" >
                            <button class="btn btn-primary me-2" id="filterbutton" type="button">Filter</button>

                            <a class="btn btn-soft-secondary" href="{{url('Truck/Dispatch')}}">Reset</a>
                        </div>
                    </div>
                </div>
            </form><hr>
            @php
            // use Carbon;
            use Carbon\Carbon;

                // $dispatched = DB::table('truck_dispatch as td')->join('truck as t', 't.id', '=', 'td.truck_id')->join('dispatchers as d', 'd.id', '=', 'td.dispatcher_id')
                // ->where('t.is_deleted', 0)->where('d.is_deleted', 0)->where('td.is_deleted', 0)->select('td.id','t.truck_number', 't.vin', 't.year', 't.make', 't.model', 't.plate_number', 'd.dispatcher_name')->get();
                $i = 1;
                $j = 1;
                if(isset($_GET['dispatcher'])){
                    $dispatchers=DB::table('dispatchers')->where('is_deleted',0)->where('id',$_GET['dispatcher'])->get();
                }else{
                    $dispatchers=DB::table('dispatchers')->where('is_deleted',0)->get();
                }
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
                                    <th>VIN</th>
                                    <th>Year</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Plate Number</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody id="dis-truck">
                                @foreach ($dispatchers as $item)
                                <?php
                                $numbers_string = $item->truck_id;
                                $numbers_array = array_map('intval', explode(',', $numbers_string));
                                ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$item->dispatcher_name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    {{-- <td></td> --}}
                                </tr>
                                @if($item->truck_id != null)
                                @foreach ($numbers_array as $truck_id)
                                <?php
                                    $truck_data=DB::table('truck')
                                    ->join('truck_dispatch', 'truck.id', '=', 'truck_dispatch.truck_id')
                                    ->where('truck.id', $truck_id)
                                    ->where('truck_dispatch.is_deleted', 0)
                                    ->select('truck.*')->whereBetween('truck_dispatch.created_on', [Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek(Carbon::SATURDAY)->format('Y-m-d H:i:s')])
                                    ->first();
                                    // dd($truck_data);
                                    // dd(Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d H:i:s'));
                                    if(isset($truck_data)){
?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>{{@$truck_data->truck_number}}</td>
                                            <td>{{@$truck_data->vin}}</td>
                                            <td>{{@$truck_data->year}}</td>
                                            <td>{{@$truck_data->make}}</td>
                                            <td>{{@$truck_data->model}}</td>
                                            <td>{{@$truck_data->plate_number}}</td>
                                            {{-- <td>
                                                <a href="javascript:;"  data-bs-toggle="tooltip" data-bs-placement="top" title="Dispatched"><i class="ti ti-check text-success"></i></a>

                                            </td> --}}
                                        </tr>
                                    <?php
                                    }else{

                                    }
                                ?>
                                <?php $j++ ?>
                                @endforeach
                                @endif
                                <?php $i++ ?>
                                @endforeach
                                {{-- @foreach ($dispatched as $d)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$d->dispatcher_name}}</td>
                                    <td>{{$d->truck_number}}</td>
                                    <td>{{$d->vin}}</td>
                                    <td>{{$d->year}}</td>
                                    <td>{{$d->make}}</td>
                                    <td>{{$d->model}}</td>
                                    <td>{{$d->plate_number}}</td>
                                    <td>
                                        <a href="{{url('Delete-Dispatch')}}/{{$d->id}}" class="text-danger"><i class="fa fa-trash-alt"></i></a>
                                        <a href="{{url('Truck/Accounting')}}/{{$d->id}}" class="text-primary" title="Truck Accounting"><i class="fa fa-book"></i></a>
                                    </td>
                                </tr>
                                @endforeach --}}
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
<script>
    $(document).ready(function() {
        // $('#dispatcher').on('change',function() {
        $('#filterbutton').on('click',function() {
            var id = $('#dispatcher option:selected').val();
        //     var name = $('#dispatcher option:selected').text();
        //     if(id == "" || id == null){
        //         $('.alert-danger').html(errorMessage.message).removeClass('d-none');
        //         setTimeout(function() {
        //             $('.alert-danger').addClass('d-none');
        //         }, 3000);
        //         return;
        //     }
        if(id != ''){
            $('.error').text('');
            window.location="{{url('Truck/Dispatch?dispatcher=')}}"+id
        }else{
            $('.error').text('Please select dispatcher');
        }
        //     $('#truck').html(`<option value="">Choose Truck</option>`);
        //     $('#dis-truck').html(`<tr>
        //                             <td colspan="8"><p class="text-center mt-3">Nothing to display</p></td>
        //                         </tr>`);

            // $.ajax({
            //     url: "{{ url('getNonDispatchTrucks') }}",
            //     method: "POST",
            //     data: {'id': id},
            //     success: function(response) {
            //         // console.log(response);
            //         if(response.trucks.length > 0){
            //             response.trucks.forEach(truck => {
            //                 $('#truck').append(`<option value="`+truck.id+`">`+truck.truck_number+`</option>`);
            //             });
            //         }

            //         if(response.avail.length > 0){
            //             var i = 1;
            //             $('#dis-truck').html('');
            //             response.avail.forEach(a => {
            //                 $('#dis-truck').append(`
            //                     <tr>
            //                         <td>`+i+`</td>
            //                         <td>`+name+`</td>
            //                         <td>`+a.truck_number+`</td>
            //                         <td>`+a.vin+`</td>
            //                         <td>`+a.year+`</td>
            //                         <td>`+a.make+`</td>
            //                         <td>`+a.model+`</td>
            //                         <td>`+a.plate_number+`</td>
            //                         <td><a href="{{url('Delete-Dispatch')}}/`+a.id+`" class="text-danger"><i class="fa fa-trash-alt"></i></a></td>
            //                     </tr>
            //                 `);
            //                 i++;
            //             });
            //         }
            //     },
            //     error: function(xhr, status, error) {
            //         // Handle errors here
            //         var errorMessage = JSON.parse(xhr.responseText);
            //         $('.alert-danger').html(errorMessage.message).removeClass('d-none');
            //         setTimeout(function() {
            //             $('.alert-danger').addClass('d-none');
            //         }, 5000);
            //     }
            // });
            // $.ajax({
            //     url: "{{ url('getdispatchfilterdata') }}",
            //     method: "POST",
            //     data: {'id': id},
            //     success: function(response) {
            //         console.log(response)

            //     }
            // });
        });
    });
</script>
@endsection
