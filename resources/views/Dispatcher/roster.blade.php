@extends('components/header')
@section('main')
    <?php
    $today = new DateTime();

    // Clone the object to avoid modifying the original $today.
    $saturday = clone $today;
    $sunday = clone $today;
    $dayOfWeek = $today->format('w'); // 'w' gives 0 (for Sunday) through 6 (for Saturday).

    if ($dayOfWeek != 0) {
        $saturday->modify('last sunday');
    } else {
        $saturday = $today;
    }

    if ($dayOfWeek == 6) {
        // If today is Sunday, use today's date.
        $sunday = $today;
    } else {
        // If today is not Sunday, find the next Sunday after this week's Saturday.
        $sunday->modify('next saturday');
    }

    ?>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dispatcher /</span> Roster</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Dispatcher Roster
                    </div>
                </div>
            </h5>
            @php
                $dispatcher = DB::table('dispatchers')
                    ->where('is_deleted', 0)
                    ->get();
            @endphp
            <div class="container pb-4">
                @php
                    $dispatched = DB::table('truck_dispatch as td')
                        ->join('truck as t', function ($join) {
                            $join->on('t.id', '=', 'td.truck_id');
                        })
                        // ->where('td.created_on', '>=', $saturday->format('Y-m-d 00:00:00'))
                        // ->where('td.created_on', '<=', $sunday->format('Y-m-d 23:59:59'))
                        ->where('t.is_deleted', 0)
                        ->where('td.is_deleted', 0)
                        ->select('td.id', 't.truck_number', 't.vin', 'td.truck_id', 't.year', 't.make', 't.model', 't.plate_number', 'td.created_on')
                        ->get();

                    $i = 1;
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
                                    </tr>
                                </thead>
                                <tbody id="dis-truck">
                                    @foreach ($dispatched as $d)
                                        <?php $dispatcher = DB::table('dispatchers')
                                            ->whereRaw("FIND_IN_SET('" . $d->truck_id . "', truck_id)")
                                            ->first(); ?>
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ @$dispatcher->dispatcher_name }}</td>
                                            <td>{{ $d->truck_number }}</td>
                                            <td>{{ $d->created_on }}</td>
                                        </tr>
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
