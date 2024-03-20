@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Trucks /</span> Trucks</h4>
        @php
            if (isset($_GET)) {
                $sortBy = request('field');
                $sortOrder = request('orderBy');
                $start = request('start_date');
                $end = request('end_date');
                $search = @$_GET['search'];
                $perPage = 10;

                // Create the base query builder
                $truck = DB::table('truck')
                    ->where('is_deleted', 0)
                    ->where('quite', 0);

                if ($search) {
                    $truck->where(function ($query) use ($search) {
                        $query->orWhere('truck_number', 'like', '%' . @$search . '%');
                        $query->orWhere('vin', 'like', '%' . @$search . '%');
                        $query->orWhere('make', 'like', '%' . @$search . '%');
                        $query->orWhere('model', 'like', '%' . @$search . '%');
                        $query->orWhere('year', 'like', '%' . @$search . '%');
                        $query->orWhere('plate_number', 'like', '%' . @$search . '%');
                        $query->orWhere('truck_address', 'like', '%' . @$search . '%');
                        $query->orWhere('trailer', 'like', '%' . @$search . '%');
                    });
                }

                if ($start || $end) {
                    $truck->where(function ($qry) use ($start, $end) {
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
                $truck = $truck->get();
            } else {
                // If there's no search, simply paginate all results
    $truck = DB::table('truck')
        ->where('is_deleted', 0)
        ->orderBy('id', 'asc')
        ->where('quite', 0)
                    ->get();
            }

        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Trucks List
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="{{ url('Truck/Add') }}" class="btn btn-outline-primary btn-sm" style="float: right;"><i
                                class="fa fa-plus"></i> Add Truck</a>
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

            <div class="table-responsive" style="min-height: 300px">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=id">#</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=truck_number">Truck
                                    Number</a></th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=vin">VIN
                                    #</a></th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=year">Year</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=make">Make</a>
                            </th>
                            <th><a
                                    href="{{ url()->current() }}?{{ isset($_GET['search']) ? 'search=' . $_GET['search'] : '' }}&orderBy={{ @$_GET['orderBy'] == 'desc' ? 'asc' : 'desc' }}&field=model">Model</a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                        @foreach ($truck as $o)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $o->truck_number }}</td>
                                <td>{{ $o->vin }}</td>
                                <td>{{ $o->year }}</td>
                                <td>{{ $o->make }}</td>
                                <td>{{ $o->model }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url('Edit-Truck') }}/{{ $o->id }}"><i
                                                    class="ti ti-pencil me-1"></i> Edit</a>
                                            <a class="dropdown-item delete-btn" data-id="{{ $o->id }}"
                                                data-url="{{ url('Delete-Truck') }}/{{ $o->id }}"
                                                href="javascript:void(0);"><i class="ti ti-trash me-1"></i> Delete</a>
                                            <a class="dropdown-item truck-detail" href="javascript:void(0);"
                                                data-id="{{ $o->id }}"><i class="ti ti-info-circle me-1"></i>
                                                Details</a>
                                            {{-- <a class="dropdown-item" href="{{url('Quite-Truck')}}/{{$o->id}}"
                              ><i class="ti ti-alert-triangle me-1"></i> Quit</a
                            > --}}
                                            <a class="dropdown-item"
                                                href="{{ url('archive-Truck') }}/{{ $o->id }}"><i
                                                    class="ti ti-archive me-1"></i> Archived Doc</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- <p>{{ $truck->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
        </div>
        <!--/ Basic Bootstrap Table -->


    </div>
    <!-- Modal -->
    <div class="modal fade" id="truckdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Truck Detail</h5>
                    <button type="button" class="close btn btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="details">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNewCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <form method="POST" id="addNewCCForm" enctype="multipart/form-data" class="row g-3" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Upload Document</h3>
                        </div>
                        <input type="hidden" name="truck_id" id="truck_id">
                        <div class="col-12">
                            <label for="">Termination Letter</label>
                            <input type="file" name="termination_letter" id="termination_letter" class="form-control"
                                value="{{ old('termination_letter') }}" placeholder="termination_letter" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                            aria-label="Close">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('.delete-btn').on('click', function() {
            var id = $(this).data('id');
            var url = $(this).data('url');
            if (url && id) {
                $('#addNewCCModal').modal('show');
                $('#truck_id').val(id);
                $('#addNewCCForm').attr('action', url);
            }
        })
        $('.btn-close').on('click', function() {
            $('#truckdetailModal').modal('toggle');
        })
        $('tbody').on('click', '.truck-detail', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'ajax',
                method: 'POST',
                data: {
                    'id': id
                },
                url: "{{ url('getTruckDetails') }}",
                success: function(res) {
                    if (res) {
                        var inspection = '';
                        if (res.inspection != null) {
                            var link_inspection = "{{ asset('public/uploads') }}/" + res.inspection;
                            var fileNameParts = res.inspection.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                inspection = '<a href="' + link_inspection + '" download><img src="' +
                                    link_inspection + '" class="img w-100"/></a>';
                            } else {
                                inspection = '<a href="' + link_inspection +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var car_cab = '';
                        if (res.car_cab != null) {
                            var link_car_cab = "{{ asset('public/uploads') }}/" + res.car_cab;
                            var fileNameParts = res.car_cab.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                car_cab = '<a href="' + link_car_cab + '" download><img src="' +
                                    link_car_cab + '" class="img w-100"/></a>';
                            } else {
                                car_cab = '<a href="' + link_car_cab +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var report_2290 = '';
                        console.log(res.document_2290);
                        if (res.document_2290 != null) {
                            var link_document_2290 = "{{ asset('public/uploads') }}/" + res
                                .document_2290;
                            var fileNameParts = res.document_2290.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                report_2290 = '<a href="' + link_document_2290 +
                                    '" download><img src="' + link_document_2290 +
                                    '" class="img w-100"/></a>';
                            } else {
                                report_2290 = '<a href="' + link_document_2290 +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var truck_lease = '';
                        if (res.truck_lease != null) {
                            var link_truck_lease = "{{ asset('public/uploads') }}/" + res.truck_lease;
                            var fileNameParts = res.truck_lease.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                truck_lease = '<a href="' + link_truck_lease + '" download><img src="' +
                                    link_truck_lease + '" class="img w-100"/></a>';
                            } else {
                                truck_lease = '<a href="' + link_truck_lease +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var physical_damage = '';
                        if (res.physical_damage != null) {

                            var link_physical_damage = "{{ asset('public/uploads') }}/" + res
                                .physical_damage;
                            var fileNameParts = res.physical_damage.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                physical_damage = '<a href="' + link_physical_damage +
                                    '" download><img src="' + link_physical_damage +
                                    '" class="img w-100"/></a>';
                            } else {
                                physical_damage = '<a href="' + link_physical_damage +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var physical_notice = '';
                        if (res.physical_notice != null) {

                            var link_physical_notice = "{{ asset('public/uploads') }}/" + res
                                .physical_notice;
                            var fileNameParts = res.physical_notice.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                physical_notice = '<a href="' + link_physical_notice +
                                    '" download><img src="' + link_physical_notice +
                                    '" class="img w-100"/></a>';
                            } else {
                                physical_notice = '<a href="' + link_physical_notice +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var trailer_reg = '';
                        if (res.trailer_reg != null) {

                            var link_trailer_reg = "{{ asset('public/uploads') }}/" + res.trailer_reg;
                            var fileNameParts = res.trailer_reg.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                trailer_reg = '<a href="' + link_trailer_reg + '" download><img src="' +
                                    link_trailer_reg + '" class="img w-100"/></a>';
                            } else {
                                trailer_reg = '<a href="' + link_trailer_reg +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var w9 = '';
                        if (res.w9 != null) {
                            var link_w9 = "{{ asset('public/uploads') }}/" + res.w9;
                            var fileNameParts = res.w9.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                w9 = '<a href="' + link_w9 + '" download><img src="' + link_w9 +
                                    '" class="img w-100"/></a>';
                            } else {
                                w9 = '<a href="' + link_w9 +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var saftey_report = '';
                        if (res.saftey_report != null) {
                            var link_saftey_report = "{{ asset('public/uploads') }}/" + res
                                .saftey_report;
                            var fileNameParts = res.saftey_report.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                saftey_report = '<a href="' + link_saftey_report +
                                    '" download><img src="' + link_saftey_report +
                                    '" class="img w-100"/></a>';
                            } else {
                                saftey_report = '<a href="' + link_saftey_report +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var four_pics = JSON.parse(res.four_pics);
                        var imgs = "";
                        if (four_pics != null && four_pics.length > 0) {
                            four_pics.forEach(p => {
                                var img = "{{ asset('public/uploads') }}/" + p;
                                imgs += `<div class="col-md-6"><a href="` + img +
                                    `" download="download"><img src="` + img +
                                    `" class="w-100"/></a></div>`;
                            });
                        }
                        if (res.driver_name != null) {
                            driver_name = res.driver_name;
                        } else {
                            driver_name = 'Not allocated any driver';
                        }

                        function formatDate(inputDate) {
                            var parsedDate = new Date(inputDate);
                            var formattedDate = (parsedDate.getMonth() + 1).toString().padStart(2,
                                    '0') + '-' +
                                parsedDate.getDate().toString().padStart(2, '0') + '-' +
                                parsedDate.getFullYear();
                            return formattedDate;
                        }
                        $('#truckdetailModal').modal('toggle');
                        $('#details').html(
                            `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Truck #</th>
                                        <th>VIN #</th>
                                        <th>Year</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>` + res.t_num + `</td>
                                        <td>` + res.vin + `</td>
                                        <td>` + res.year + `</td>
                                        <td>` + res.make + `</td>
                                        <td>` + res.model + `</td>
                                        <td>` + res.created_on + `</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5 class="mb-1">Owner Name</h5><p>` + res.owner_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Driver Name</h5><p>` + driver_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Plate Number</h5><p>` + res.plate_number + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">2290 Renewal Date</h5><p>` + formatDate(res
                                .renewal_date_2290) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Truck Address</h5><p>` + res.truck_address + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Trailer</h5><p>` + res.trailer + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Card Renewal Date</h5><p>` + formatDate(res
                                .card_renew_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Sticker Renewal Date</h5><p>` + formatDate(res
                                .sticker_renew_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Insurance Name</h5><p>` + res
                            .damage_insurance_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Insurance Policy Number</h5><p>` + res
                            .insurance_policy_number + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Effective Date</h5><p>` + formatDate(res
                                .damage_effective_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Expiry Date</h5><p>` + formatDate(res
                                .damage_expiry_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Trailer Registration Renewal Date</h5><p>` +
                            formatDate(res.trailer_reg_renew_date) + `</p></div>

                            <div class="col-md-12"><h5 class="mb-1">2290:</h5>` + report_2290 + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Inspection:</h5>` + inspection + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Cab Card:</h5>` + car_cab + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Truck Lease:</h5>` + truck_lease + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Physical Damage:</h5>` + physical_damage + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Physical Notice:</h5>` + physical_notice + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Trailer Registration:</h5>` + trailer_reg + `</div>
                            <div class="col-md-12"><h5 class="mb-1">W9:</h5>` + w9 + `</div>
                            <div class="col-md-12"><h5 class="mb-1">Saftey Report:</h5>` + saftey_report + `</div>
                        </div>
                        <div class="row"><div class="col-md-12"><h5 class="mb-1">Four Truck Pics:</h5></div>` + imgs +
                            `</div>`


                        );
                    }
                }
            });
        });
    </script>
@endsection
