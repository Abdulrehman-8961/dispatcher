@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dispatcher /</span> Old Dispatcher</h4>
        @php
            if (isset($_GET)) {
                $sortBy = request('field');
                $sortOrder = request('orderBy');
                $start = request('start_date');
                $end = request('end_date');
                $search = @$_GET['search'];
                $perPage = 10;

                // Create the base query builder
                $dispatcher = DB::table('dispatchers')->where('is_deleted', 1);

                if ($search) {
                    $dispatcher->where(function ($query) use ($search) {
                        $query->orWhere('dispatcher_name', 'like', '%' . @$search . '%');
                        $query->orWhere('phone', 'like', '%' . @$search . '%');
                        $query->orWhere('email', 'like', '%' . @$search . '%');
                        $query->orWhere('salary', 'like', '%' . @$search . '%');
                        $query->orWhere('address', 'like', '%' . @$search . '%');
                        $query->orWhere('routing_number', 'like', '%' . @$search . '%');
                        $query->orWhere('account_number', 'like', '%' . @$search . '%');
                        $query->orWhere('driver_license_number', 'like', '%' . @$search . '%');
                        $query->orWhere('ssn_number', 'like', '%' . @$search . '%');
                    });
                }

                if ($start || $end) {
                    $dispatcher->where(function ($qry) use ($start, $end) {
                        if ($start) {
                            $qry->where('created_on', '>=', @$start . ' 00:00:01');
                        }
                        if ($end) {
                            $qry->where('created_on', '<=', @$end . ' 23:59:00');
                        }
                    });
                }

                if (isset($sortBy)) {
                    $truck->orderBy($sortBy, $sortOrder);
                }
                // Retrieve the results with pagination
                $dispatcher = $dispatcher->get();
            } else {
                // If there's no search, simply paginate all results
    $dispatcher = DB::table('dispatchers')
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
                        Old Dispatcher List
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
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=dispatcher_name">Dispatcher
                                    Name</a></th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=phone">Phone</a>
                            </th>
                            <th>Address</th>
                            <th>Salary</th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=email">Email</a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                        @foreach ($dispatcher as $o)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $o->dispatcher_name }}</td>
                                <td>{{ $o->phone }}</td>
                                <td>{{ $o->address }}</td>
                                <td>{{ $o->salary }}</td>
                                <td>{{ $o->email }}</td>
                                <td>
                                    <button data-id="{{ $o->id }}"
                                        class="badge rounded-pill btn-label-primary waves-effect rehire_btn">Re-Hire</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- {{ $dispatcher->appends(request()->input())->links() }} --}}
            {{-- {{ $dispatcher->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
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
            window.location = "{{ url('rehire/dispatcher') }}/" + rehireId;
        })

        $('.btn-close').on('click', function() {
            $('#ownerdetailModal').modal('toggle');
        })
        $('tbody').on('click', '.dispatcher-detail', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'ajax',
                method: 'POST',
                data: {
                    'id': id
                },
                url: "{{ url('getDispatcherDetails') }}",
                success: function(res) {
                    if (res) {
                        var driver_licenses_path = "{{ asset('public/uploads') }}/" + res
                            .driver_licenses_path;
                        var ssn_pic_path = "{{ asset('public/uploads') }}/" + res.ssn_pic_path;
                        $('#ownerdetailModal').modal('toggle');
                        $('#details').html(
                            `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Dispatcher Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Time Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>` + res.dispatcher_name + `</td>
                                        <td>` + res.phone + `</td>
                                        <td>` + res.email + `</td>
                                        <td>` + res.address + `</td>
                                        <td>` + res.created_on + `</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5>Salary</h5><p>` + res.salary + `</p></div>
                            <div class="col-md-6"><h5>Routing Number</h5><p>` + res.routing_number + `</p></div>
                            <div class="col-md-6"><h5>Account Number</h5><p>` + res.account_number + `</p></div>
                            <div class="col-md-6"><h5>Driver License Number</h5><p>` + res.driver_license_number + `</p></div>
                            <div class="col-md-12"><h5>Driver License:</h5><a href="` + driver_licenses_path +
                            `" download="download"><img src="` + driver_licenses_path + `" class="img w-100"/></a></div>
                            <div class="col-md-12"><h5>SSN:</h5><a href="` + ssn_pic_path +
                            `" download="download"><img src="` + ssn_pic_path + `" class="img w-100"/></a></div>
                        </div>
                        `


                        );
                    }
                }
            });
        });
    </script>
@endsection
