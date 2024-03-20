@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Drivers /</span> Driver Roster</h4>

    <!-- Driver Roster Table -->
    <div class="mb-4">
        <button class="btn btn-primary" id="activeBtn">Active</button>
        <button class="btn btn-secondary" id="inactiveBtn">Inactive</button>
    </div>
    <div class="card">
        {{-- <h5 class="card-header">Driver Roster</h5> --}}

        <div class="container">
            <div class="row">
                <div id="active_div" class="col-md-12 mb-4">
                    <h5 class="mb-0 mt-4">Active Drivers</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Driver's Name</th>
                                <th>Driver's License Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($activeDrivers as $driver)
                            <tr>
                                <td>{{ $driver->driver_name }}</td>
                                <td>{{ $driver->driver_license }}</td>
                                <td>
                                    <a class="btn btn-danger" href="{{url('De-ActivateDriver')}}/{{$driver->id}}">De-Activate</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="inactive_div" class="col-md-12 mb-4 d-none">
                    <h5 class="mb-0 mt-4">Inactive Drivers</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Driver's Name</th>
                                <th>Driver's License Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inactiveDrivers as $driver)
                            <tr>
                                <td>{{ $driver->driver_name }}</td>
                                <td>{{ $driver->driver_license }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{url('ActivateDriver')}}/{{$driver->id}}">Activate</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--/ Driver Roster Table -->
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $('#activeBtn').on('click',function(){
        $('#active_div').removeClass('d-none')
        $('#inactive_div').addClass('d-none')
    })

    $('#inactiveBtn').on('click',function(){
        $('#inactive_div').removeClass('d-none')
        $('#active_div').addClass('d-none')
    })
</script>
@endsection
