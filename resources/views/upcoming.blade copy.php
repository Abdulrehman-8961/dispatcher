@extends('components/header')
@section('main')
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Upcomings</h4>
        @php
            $results = DB::table('truck')
                ->where(function ($query) {
                    $query
                        ->whereDate('card_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('trailer_reg_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('card_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('damage_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('renewal_date_2290', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('sticker_renew_date', '<=', date('Y-m-d', strtotime('+30 days')))
                        ->orWhereDate('quite_date', '<=', date('Y-m-d', strtotime('-8 months')));
                })
                ->where('status', 'active')
                ->where('is_deleted', 0)
                ->where('quite', 0)
                ->get();
            // dd($results);
            $results1 = DB::table('drivers')
                ->where(function ($query) {
                    $query->whereDate('license_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')))->orWhereDate('medical_expiry_date', '<=', date('Y-m-d', strtotime('+30 days')));
                })
                ->where('is_deleted', 0)
                ->get();

            $array = [];

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
                                        <tr>
                                            <td>{{ date('M-d-Y', strtotime($i['expiry_date'])) }}</td>
                                            <td><span>
                                                    <p class="mb-0">{{ $i['table_name'] }}: {{ $i['id'] }}</p>
                                                    <p class="mb-0">Document: {{ $i['name'] }}</p>
                                                </span></td>
                                            <td><a href="{{ $i['link'] }}"
                                                    class="badge bg-label-primary rounded-pill ms-auto fs-6">Resolve</a>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script></script>
@endsection
