@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Escrow Return</h4>
        @php
            $result = DB::table('truck_accounting')
                ->select(DB::raw('SUBSTRING(name, -4) as last_four_digits'), DB::raw('MAX(id) as id'))
                ->groupBy(DB::raw('SUBSTRING(name, -4)'))
                ->orderBy('name', 'DESC')
                ->get();
            if (isset($_GET['year'])) {
                $year = DB::table('truck_accounting')
                    ->where('id', $_GET['year'])
                    ->first();
                $year_name = substr($year->name, -4);
                // dd($year_name);
                $truck = DB::table('truck')
                    ->where('is_deleted', 0)
                    ->orderBy('id', 'asc')
                    ->where('escrow_return', 1)
                    ->get();
            } else {
                // If there's no search, simply paginate all results
    $truck = DB::table('truck')
        ->where('is_deleted', 0)
        ->orderBy('id', 'asc')
        ->where('escrow_return', 1)
                    ->get();
            }

        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Escrow Return List
                    </div>
                </div>
            </h5>
            <form>
                <div class="row ps-3">
                    <div class="col-md-3">
                        <select name="year" class="form-control" id="year" required>
                            <option value="">Choose Year</option>
                            @foreach ($result as $item)
                                <option value="{{ $item->id }}" @if (@request('year') == $item->id) selected @endif>
                                    {{ $item->last_four_digits }}</option>
                            @endforeach
                        </select>
                    </div>
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
                            <th>#</th>
                            <th>Truck</th>
                            <th>Escrow Amount</th>
                            <th>Quite Date</th>
                            <th>90 Day Return Day</th>
                            <th>Return</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $all_amount = 0;
                        @endphp
                        @foreach ($truck as $o)
                            @php
                                $escrow_amount = DB::table('truck_accounting')
                                    ->where('truck_id', $o->id)
                                    ->Join('truck_expense', 'truck_accounting.id', '=', 'truck_expense.accounting_id')
                                    ->where('truck_expense.category', 14);
                                if (isset($_GET['year'])) {
                                    $escrow_amount = $escrow_amount->where(DB::raw('SUBSTRING(truck_accounting.name, -4)'), $year_name);
                                }

                                $escrow_amount = $escrow_amount->sum('truck_expense.amount');
                                if (isset($o->quite_date)) {
                                    $givenDate = $o->quite_date;
                                    $originalDateTime = new DateTime($givenDate);
                                    $modifiedDateTime = $originalDateTime->modify('+90 days');
                                    $modifiedDate = $modifiedDateTime->format('M-d-Y');
                                } else {
                                    $modifiedDate = '';
                                }

                                $all_amount = $all_amount + $escrow_amount;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $o->truck_number }}</td>
                                <td>{{ $escrow_amount }}</td>
                                <td><?php if (isset($o->quite_date)) {
                                    echo date('M-d-Y', strtotime($o->quite_date));
                                } ?> </td>
                                <td>{{ $modifiedDate }}</td>
                                <td>
                                    <input type="checkbox" class="escrow_return" data-id="{{ $o->id }}">
                                </td>
            </div>
        </div>
        </td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td><b>Total Escrow</b></td>
            <td><b>{{ $all_amount }}</b></td>
        </tr>
        </tbody>
        </table>
    </div>
    {{-- {{ $truck->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
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
        $('body').on('click', '.escrow_return', function() {
            var truckId = $(this).data("id");
            window.location = "{{ url('go/escrow/returnToEscrow') }}/" + truckId;
        })
    </script>
@endsection
