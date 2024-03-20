@extends('components/header')
@section('main')
    <style>
        .background-color {
            color: #fff200 !important;
        }
    </style>
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-4">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Upcomings</h4>

            </div>
            <div class="col-md-7">

            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#basicModal">
                    <i class="ti ti-plus"></i>
                </button>
            </div>
        </div>
        @php
            $notifications = DB::table('notifications')
                ->where(function ($query) {
                    $query->whereDate('date', '<=', date('Y-m-d', strtotime('+30 days')));
                })
                ->where('is_resolved', 0)
                ->get();
            $results = DB::table('truck')
                ->where(function ($query) {
                    $query
                        ->whereDate('card_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('trailer_reg_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('card_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('damage_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('renewal_date_2290', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('sticker_renew_date', '<=', date('Y-m-d', strtotime('+30 days')));
                })
                ->where('status', 'active')
                ->where('is_deleted', 0)
                ->where('quite', 0)
                // ->orWhere(function ($query) {
                //     $query
                //         ->where('is_deleted', 1)
                //         ->where('quite', 1)
                //         ->whereDate('quite_date', '<=', date('Y-m-d', strtotime('-8 months')));
                // })
                ->get();
            $quite_date = DB::table('truck')
                ->where(function ($query) {
                    $query
                    ->whereDate('quite_date', '<=', date('Y-m-d', strtotime('-60 days')));
                })
                ->where('escrow_return', 0)
                ->where('is_deleted', 1)
                ->where('quite', 1)
                ->get();
            $results1 = DB::table('drivers')
                ->where(function ($query) {
                    $query->whereDate('license_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')))->orWhereDate('medical_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')));
                })
                ->where('is_deleted', 0)
                ->get();

            $array = [];

            foreach ($results as $r) {
                if ($r->card_renew_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->card_renew_date,
                        'name' => 'Cab Card Expire',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('Edit-Truck') . '/' . $r->id,
                    ];
                }

                if ($r->renewal_date_2290 <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->renewal_date_2290,
                        'name' => '2290 Form Expire',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('Edit-Truck') . '/' . $r->id,
                    ];
                }

                if ($r->sticker_renew_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->sticker_renew_date,
                        'name' => 'Sticker renewal',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('Edit-Truck') . '/' . $r->id,
                    ];
                }

                // if ($r->quite_date <= date('Y-m-d', strtotime('-8 months'))) {
                //     $givenDate = $r->quite_date;
                //     $originalDateTime = new DateTime($givenDate);
                //     $modifiedDateTime = $originalDateTime->modify('+90 days');
                //     $modifiedDate = $modifiedDateTime->format('M-d-Y');
                //     $array[] = [
                //         'expiry_date' => $modifiedDate,
                //         'name' => 'Escrow ',
                //         'table_name' => 'Truck',
                //         'id' => $r->truck_number,
                //         'link' => url('Edit-Truck') . '/' . $r->id,
                //     ];
                // }

                if ($r->trailer_reg_renew_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->trailer_reg_renew_date,
                        'name' => 'Trailer Registration Expire',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('Edit-Truck') . '/' . $r->id,
                    ];
                }

                if ($r->damage_expiry_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->damage_expiry_date,
                        'name' => 'Physical Damage Expire',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('Edit-Truck') . '/' . $r->id,
                    ];
                }
            }
            foreach ($quite_date as $r) {
                if ($r->quite_date <= date('Y-m-d', strtotime('-60 days'))) {
                    $givenDate = $r->quite_date;
                    $originalDateTime = new DateTime($givenDate);
                    $modifiedDateTime = $originalDateTime->modify('+90 days');
                    $modifiedDate = $modifiedDateTime->format('M-d-Y');
                    $array[] = [
                        'expiry_date' => $modifiedDate,
                        'name' => 'Escrow ',
                        'table_name' => 'Truck',
                        'id' => $r->truck_number,
                        'link' => url('go/escrow/return') . '/' . $r->id,
                    ];
                }
            }

            foreach ($results1 as $r) {
                if ($r->license_expiry_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->license_expiry_date,
                        'name' => 'License Expiry',
                        'table_name' => 'Driver',
                        'id' => $r->driver_name,
                        'link' => url('Edit-Driver') . '/' . $r->id,
                    ];
                }

                if ($r->medical_expiry_date <= date('Y-m-d', strtotime('+30 days'))) {
                    $array[] = [
                        'expiry_date' => $r->medical_expiry_date,
                        'name' => 'Medical Certificate Expire',
                        'table_name' => 'Driver',
                        'id' => $r->driver_name,
                        'link' => url('Edit-Driver') . '/' . $r->id,
                    ];
                }
            }
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

            // Count the number of elements in the $array
            $arrayCount = count($array);

            // Output the count
            // dd($arrayCount);

            // Function to compare dates for sorting
            function compareExpiryDate($a, $b)
            {
                return strtotime($a['expiry_date']) - strtotime($b['expiry_date']);
            }

            // Sort the array using the custom comparison function
            usort($array, 'compareExpiryDate');

        @endphp
        <!-- Template Documents List (if needed) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th>Expiry Date</th>
                                        <th>Notification</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($array as $i)
                                        @php
                                            $expiryDate = strtotime($i['expiry_date']);
                                            $currentDate = strtotime(date('M-d-Y'));
                                            $thirtyDaysFromNow = strtotime('+30 days', $currentDate);
                                        @endphp
                                        <tr
                                            class="@if ($expiryDate < $currentDate) text-danger @elseif($expiryDate <= $thirtyDaysFromNow) text-warning @endif">
                                            <td>{{ date('M-d-Y', strtotime($i['expiry_date'])) }}</td>
                                            <td><span>
                                                    <p class="mb-0">{{ $i['table_name'] }}: {{ $i['id'] }}</p>
                                                    <p class="mb-0">Document: {{ $i['name'] }}</p>
                                                </span></td>
                                            <td><a href="{{ $i['link'] }}"
                                                    class="badge @if ($expiryDate < $currentDate) bg-label-danger @elseif($expiryDate <= $thirtyDaysFromNow) bg-label-warning @else bg-label-primary @endif rounded-pill ms-auto fs-6">Resolve</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $all_trucks = DB::table('truck')
            ->where('is_deleted', 0)
            ->get();
    @endphp

    <!-- Modal -->
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('save/notification') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="document" class="form-label">Document</label>
                                <input type="text" id="document" name="document" class="form-control" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="emailBasic" class="form-label">Truck</label>
                                <select name="truck" class="form-control select2" id="dispatcher_account" required>
                                    @foreach ($all_trucks as $t)
                                        <option value="{{ $t->truck_number }}">{{ $t->truck_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mb-0">
                                <label for="dobBasic" class="form-label">Date</label>
                                <input type="date" id="date" name="date" class="form-control"
                                    placeholder="DD / MM / YY" required />
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script></script>
@endsection
