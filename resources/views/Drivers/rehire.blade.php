@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Old Drivers</h4>
        @php
            if (isset($_GET)) {
                $sortBy = request('field');
                $sortOrder = request('orderBy');
                $start = request('start_date');
                $end = request('end_date');
                $search = @$_GET['search'];
                $perPage = 100;

                // Create the base query builder
                $drivers = DB::table('drivers')->where('is_deleted', 1);

                if ($search) {
                    $drivers->where(function ($query) use ($search) {
                        $query->orWhere('driver_name', 'like', '%' . @$search . '%');
                        $query->orWhere('truck_number', 'like', '%' . @$search . '%');
                        $query->orWhere('email', 'like', '%' . @$search . '%');
                        $query->orWhere('phone', 'like', '%' . @$search . '%');
                        $query->orWhere('address', 'like', '%' . @$search . '%');
                        $query->orWhere('driver_license', 'like', '%' . @$search . '%');
                    });
                }

                if ($start || $end) {
                    $drivers->where(function ($qry) use ($start, $end) {
                        if ($start) {
                            $qry->where('created_on', '>=', @$start . ' 00:00:01');
                        }
                        if ($end) {
                            $qry->where('created_on', '<=', @$end . ' 23:59:00');
                        }
                    });
                }

                if (isset($sortBy)) {
                    $drivers->orderBy($sortBy, $sortOrder);
                }

                // Retrieve the results with pagination
                $drivers = $drivers->get();
            } else {
                // If there's no search, simply paginate all results
    $drivers = DB::table('drivers')
        ->where('is_deleted', 1)
        ->orderBy('id', 'asc')
                    ->get();
            }

        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Old Drivers List
                    </div>
                </div>
            </h5>

            <form>
                <div class="row">
                    <div class="col-md-3 offset-1">
                        <input type="date" name="start_date" class="form-control" value="{{ @$_GET['start_date'] }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control" value="{{ @$_GET['end_date'] }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" value="{{ @$_GET['search'] }}"
                            placeholder="Search Anything...">
                    </div>
                    <input type="hidden" name="orderBy" value="{{ request('orderBy') }}">
                    <input type="hidden" name="field" value="{{ request('field') }}">
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary">Search</button>
                    </div>
                </div>
                <hr>
            </form>

            <div class="table-responsive ">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=id">#</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=driver_name">Driver
                                    Name</a></th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=truck_id">Truck
                                    Number</a></th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=email">Email</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=phone">Phone</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=driver_license">License</a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                        @foreach ($drivers as $o)
                            @php
                                $truck = DB::table('truck')
                                    ->where('id', $o->truck_id)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $o->driver_name }}</td>
                                <td>{{ @$truck->truck_number }}</td>
                                <td>{{ $o->email }}</td>
                                <td>{{ $o->phone }}</td>
                                <td>{{ $o->driver_license }}</td>
                                <td><button data-id="{{ $o->id }}"
                                        class="badge rounded-pill btn-label-primary waves-effect rehire_btn">Re-Hire</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- {{ $drivers->appends(request()->input())->links() }} --}}
            {{-- {{ $drivers->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
        </div>
        <!--/ Basic Bootstrap Table -->


    </div>
    <!-- Modal -->
    <div class="modal fade" id="ownerdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Owner Detail</h5>
                    <button type="button" class="close btn btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="details">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('body').on('click', '.rehire_btn', function() {
            var rehireId = $(this).data("id");
            window.location = "{{ url('rehire/driver') }}/" + rehireId;
        })

        $('.btn-close').on('click', function() {
            $('#ownerdetailModal').modal('toggle');
        })
        $('tbody').on('click', '.driver-detail', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'ajax',
                method: 'POST',
                data: {
                    'id': id
                },
                url: "{{ url('getDriverDetails') }}",
                success: function(res) {
                    console.log(res);
                    if (res) {
                        var medical_card = "{{ asset('public/uploads') }}/" + res.medical_card;
                        var drug_test = "{{ asset('public/uploads') }}/" + res.drug_test;
                        var license = "{{ asset('public/uploads') }}/" + res.license;
                        var mvr = "{{ asset('public/uploads') }}/" + res.mvr;
                        var employment_application = "{{ asset('public/uploads') }}/" + res
                            .employment_application;
                        var clearing_house = "{{ asset('public/uploads') }}/" + res.clearing_house;
                        var orientation = "{{ asset('public/uploads') }}/" + res.orientation;
                        var emergency_contact = "{{ asset('public/uploads') }}/" + res
                            .emergency_contact;
                        var ssn_file = "{{ asset('public/uploads') }}/" + res.ssn_file;
                        $('#ownerdetailModal').modal('toggle');
                        $('#details').html(
                            `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Driver Name</th>
                                        <th>Truck Number</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>License</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>` + res.driver_name + `</td>
                                        <td>` + res.truck_number + `</td>
                                        <td>` + res.phone + `</td>
                                        <td>` + res.email + `</td>
                                        <td>` + res.driver_license + `</td>
                                        <td>` + res.created_on + `</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5>Address</h5><p>` + res.address + `</p></div>
                            <div class="col-md-6"><h5>License Issue Date</h5><p>` + res.license_issue_date + `</p></div>
                            <div class="col-md-6"><h5>License Expiry Date</h5><p>` + res.license_expiry_date + `</p></div>
                            <div class="col-md-6"><h5>Medical Issue Date</h5><p>` + res.medical_issue_date + `</p></div>
                            <div class="col-md-6"><h5>Medical Expiry Date</h5><p>` + res.medical_expiry_date + `</p></div>
                            <div class="col-md-6"><h5>SSN #</h5><p>` + res.ssn + `</p></div>
                            <div class="col-md-12"><hr><h5>Medical Card:</h5><a href="` + medical_card +
                            `" download="download"><img src="` + medical_card + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>Drug Test:</h5<a href="` + drug_test +
                            `" download="download">><img src="` + drug_test + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>License:</h5><a href="` + license +
                            `" download="download"><img src="` + license + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>MVR:</h5><a href="` + mvr + `" download="download"><img src="` +
                            mvr + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>Employment Application:</h5><a href="` +
                            employment_application + `" download="download"><img src="` +
                            employment_application + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>Clearing House:</h5><a href="` + clearing_house +
                            `" download="download"><img src="` + clearing_house + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>Orientation:</h5><a href="` + orientation +
                            `" download="download"><img src="` + orientation + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>Emerygency Contact:</h5><a href="` + emergency_contact +
                            `" download="download"><img src="` + emergency_contact + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>SSN:</h5><a href="` + ssn_file +
                            `" download="download"><img src="` + ssn_file + `" class="img w-100"/></a></div>
                        </div>
                        `


                        );
                    }
                }
            });
        });
    </script>
@endsection
