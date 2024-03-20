@extends('components/header')
@section('main')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Website Analytics -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center"> Welcome {{ Auth::user()->name }}!</h3>
                        {{-- <h3 class="text-center">Welcome {{ Auth::user()->name }}</h3> --}}
                        {{-- <a href="{{url('/1099/pdf')}}" class="btn btn-danger">pdf</a> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Owners') }}" class="text-dark">Owners</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-certificate ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Truck') }}" class="text-dark">Truck</a></h5>
                            {{-- <small>13</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-truck-delivery ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Drivers') }}" class="text-dark">Driver</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-steering-wheel ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Dispatcher') }}" class="text-dark">Dispatcher</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-transfer-in ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('View/Company') }}" class="text-dark">Company</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-building ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Truck/Dispatch') }}" class="text-dark">Dispatch</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-truck-delivery ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Truck/Accounting') }}" class="text-dark">Truck
                                    Account</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calculator ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Documents') }}" class="text-dark">Documents</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-file ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $notifications = DB::table('notifications')
                    ->where(function ($query) {
                        $query->whereDate('date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))]);
                    })
                    ->where('is_resolved', 0)
                    ->get();
                $results = DB::table('truck')
                    ->where(function ($query) {
                        $query
                            ->whereBetween('card_renew_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])
                            ->orWhereBetween('trailer_reg_renew_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])
                            ->orWhereBetween('damage_expiry_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])
                            ->orWhereBetween('renewal_date_2290', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])
                            ->orWhereBetween('sticker_renew_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))]);
                    })
                    ->where('status', 'active')
                    ->where('is_deleted', 0)
                    ->where('quite', 0)
                    ->get();

                // dd($results);
                $results1 = DB::table('drivers')
                    ->where(function ($query) {
                        $query->whereBetween('license_expiry_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])->orWhereBetween('medical_expiry_date', [date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))]);
                    })
                    ->where('is_deleted', 0)
                    ->get();

                $array = [];

                foreach ($notifications as $r) {
                    if ($r->date <= date('Y-m-d', strtotime('+30 days'))) {
                        $array[] = [
                            'expiry_date' => $r->date,
                            'name' => $r->document,
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('update/notification') . '/' . $r->id,
                        ];
                    }
                }

                foreach ($results as $r) {
                    if ($r->card_renew_date <= date('Y-m-d', strtotime('+30 days')) and $r->card_renew_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->card_renew_date,
                            'name' => 'Cab Card Expire',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }

                    if ($r->renewal_date_2290 <= date('Y-m-d', strtotime('+30 days')) and $r->renewal_date_2290 > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->renewal_date_2290,
                            'name' => '2290 Form Expire',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }

                    if ($r->sticker_renew_date <= date('Y-m-d', strtotime('+30 days')) and $r->sticker_renew_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->sticker_renew_date,
                            'name' => 'Sticker renewal',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }

                    if ($r->quite_date <= date('Y-m-d', strtotime('-8 months'))) {
                        $givenDate = $r->quite_date;
                        $originalDateTime = new DateTime($givenDate);
                        $modifiedDateTime = $originalDateTime->modify('+90 days');
                        $modifiedDate = $modifiedDateTime->format('M-d-Y');
                        $array[] = [
                            'expiry_date' => $modifiedDate,
                            'name' => 'Escrow ',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }

                    if ($r->trailer_reg_renew_date <= date('Y-m-d', strtotime('+30 days')) and $r->trailer_reg_renew_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->trailer_reg_renew_date,
                            'name' => 'Trailer Registration Expire',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }

                    if ($r->damage_expiry_date <= date('Y-m-d', strtotime('+30 days')) and $r->damage_expiry_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->damage_expiry_date,
                            'name' => 'Physical Damage Expire',
                            'table_name' => 'Truck',
                            'id' => $r->truck_number,
                            'link' => url('Edit-Truck') . '/' . $r->id,
                        ];
                    }
                }

                foreach ($results1 as $r) {
                    if ($r->license_expiry_date <= date('Y-m-d', strtotime('+30 days')) and $r->license_expiry_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->license_expiry_date,
                            'name' => 'License Expiry',
                            'table_name' => 'Driver',
                            'id' => $r->driver_name,
                            'link' => url('Edit-Driver') . '/' . $r->id,
                        ];
                    }

                    if ($r->medical_expiry_date <= date('Y-m-d', strtotime('+30 days')) and $r->medical_expiry_date > date('Y-m-d')) {
                        $array[] = [
                            'expiry_date' => $r->medical_expiry_date,
                            'name' => 'Medical Certificate Expire',
                            'table_name' => 'Driver',
                            'id' => $r->driver_name,
                            'link' => url('Edit-Driver') . '/' . $r->id,
                        ];
                    }
                }

                // Count the number of elements in the $array
                $arrayCount = count($array);
            @endphp
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Upcomings') }}" class="text-dark me-1">Upcomings</a>
                                @if ($arrayCount > 0)
                                    <small class="text-primary">{{ $arrayCount }}</small>
                                @endif
                            </h5>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calendar ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $total_invoices = DB::table('invoices')->where('status', 'pending')->where('is_deleted', 0)->count();
            @endphp
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('/Invoice/Create') }}"
                                    class="text-dark me-2">Invoice</a>
                                @if ($total_invoices > 0)
                                    <small class="text-primary">{{ $total_invoices }}</small>
                                @endif
                            </h5>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-receipt ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('1099') }}" class="text-dark">1099</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calendar ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Categories') }}" class="text-dark">Categories</a>
                            </h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-apps ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Dispatch-Statement') }}" class="text-dark">Dispatch
                                    Statement</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-book-2 ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('YTD') }}" class="text-dark">YTD</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calendar-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('escrow') }}" class="text-dark">Escrow</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calendar-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Accident-Report') }}" class="text-dark">Accident
                                    Report</a></h5>
                            {{-- <small>CPU Usage</small> --}}
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-calendar ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="card-title mb-0">
                            <h5 class="mb-0 me-2"><a href="{{ url('Accident-Report') }}" class="text-dark">Users</a></h5>

                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded-pill p-2">
                                <i class="ti ti-users ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="card">
                <h5 class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            Notes List
                        </div>
                        <div class="col-md-6 text-right float-right">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#notesModal" style="float: right;"><i class="fa fa-plus"></i> Add
                                Note</button>
                        </div>
                    </div>
                </h5>
                @php
                    $notes = DB::table('notes')->where('is_deleted', 0)->get();

                @endphp
                <div class="table-responsive" style="min-height: 300px">
                    <table class="table mb-5">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 70%;">Notes</th>
                                <th>Created on</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">

                            @foreach ($notes as $o)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $o->note }}</td>
                                    <td>{{ date('M-d-Y', strtotime($o->created_at)) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item edit-note" href="javascript:void(0);"
                                                    data-note="{{ $o->note }}" data-id="{{ $o->id }}"><i
                                                        class="ti ti-pencil me-1"></i> Edit</a>
                                                <a class="dropdown-item"
                                                    href="{{ url('Delete-note') }}/{{ $o->id }}"><i
                                                        class="ti ti-trash me-1"></i> Delete</a>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="notesModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel1">Add Note</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ url('add/note') }}" method="POST">
                            @csrf
                            <input type="hidden" id="update_id" name="update_id" value="0">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="notes" class="form-label">Note Description</label>
                                        <textarea rows="4" id="notes" name="notes" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('.edit-note').on('click', function() {
            var id = $(this).data('id');
            var note = $(this).data('note');
            if (note && id) {
                $('#notesModal').modal('show');
                $('#update_id').val(id);
                $('#notes').val(note);
            }
        })
    </script>
@endsection
