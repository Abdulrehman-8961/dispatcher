@extends('components/header')
@section('main')
    <style>
        .background-color {
            background-color: #fff200 !important;
        }
    </style>
    @php
        $dispatcher = DB::table('dispatchers')
        ->where('is_deleted',0)
        ->get();
        $years = DB::table('truck_dispatch')
            ->select(DB::raw('YEAR(created_on) as year'))
            ->distinct()
            ->get();

        $currentYear = now()->year;
        $currentMonth = now()->month;
        $currentYearExists = '0';

        $truck = DB::table('truck')
            ->where('is_deleted', 0)
            ->get();

        $currentDate = new DateTime();
        if (isset($_GET['year']) && $_GET['year'] != "") {
            $yearNumber = $_GET['year'];
        } else {
            $yearNumber = $currentDate->format('o');
        }
        // dd($yearNumber);
        $firstDay = new DateTime($yearNumber . '-01-01');
        $lastDay = new DateTime($currentDate->format('Y-m-t'));

        $weeks = [];

        while ($firstDay <= $lastDay) {
            $endOfWeek = clone $firstDay;
            $endOfWeek->modify('+6 days');

            // Store the start and end dates along with the month in the array
            $weeks[] = [
                'start' => $firstDay->format('Y-m-d H:i:s'),
                'end' => $endOfWeek->format('Y-m-d H:i:s'),
                'month' => $firstDay->format('m'),
            ];

            $firstDay->modify('+7 days');
        }

        // Group weeks by month
        $weeksByMonth = [];

        foreach ($weeks as $week) {
            $month = $week['month'];
            $weeksByMonth[$month][] = $week;
        }

        // $weeksByMonth now contains an associative array where keys are months and values are arrays of weeks within each month
        // dd($weeksByMonth);
        if (isset($_GET['month'])) {
            $current_month = $_GET['month'];
        } else {
            $current_month = date('m');
        }
        // dd($current_month);
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Truck /</span> Dispatch schedule</h4>

        <!-- Buttons for Filtering Trucks -->
        {{-- <div class="mb-4">
        <button class="btn btn-primary" id="activeBtn">Active</button>
        <button class="btn btn-secondary" id="inactiveBtn">Inactive</button>
        <button class="btn btn-info" id="underDispatchBtn">Under Dispatch</button>
        <button class="btn btn-warning" id="notUnderDispatchBtn">Not Under Dispatch</button>
    </div> --}}

        <!-- Truck Table -->
        <div class="card">
            <div class="card-header row">
                <div class="col-md-3">
                    <h5 class="card-title">Dispatch</h5>
                </div>
                <form action="">
                    <div class="row">
                        <div class="col-md-5"></div>
                        <div class="col-md-3">
                            <select class="form-select me-2 select2" name="year" id="year"
                                onchange="updateMonths(this.value)">
                                <option value="">Select Year</option>
                                @foreach ($years as $year)
                                    @if ($year->year == $currentYear)
                                        @php $currentYearExists = "1"; @endphp
                                    @endif
                                    <option value="{{ $year->year }}"
                                        {{ isset($_GET['year']) ? ($_GET['year'] == $year->year ? 'selected' : '') : ($currentYear == $year->year ? 'selected' : '') }}>
                                        {{ $year->year }}</option>
                                @endforeach
                                @if ($currentYearExists == '0')
                                    <option value="{{ $currentYear }}"
                                        {{ isset($_GET['year']) ? ($_GET['year'] == $currentYear ? 'selected' : '') : '' }}>
                                        {{ $currentYear }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select me-2 select2" name="month" id="month">

                            </select>

                        </div>
                        <div class="col-md-1">

                            <button class="btn btn-outline-primary">Filter</button>
                        </div>



                </form>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 offset-md-8">

                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="truckTable">
                    <thead class="text-center">
                        <tr>
                            <th>Truck Number</th>
                            @php
                                $sr = 1;
                            @endphp
                            @foreach ($weeksByMonth[$current_month] as $week)
                                <th>week {{ $sr++ }} ({{ date('M d', strtotime($week['start'])) }} -
                                    {{ date('M d', strtotime($week['end'])) }})</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($truck as $t)
                            <tr class="text-center">
                                <td>{{ $t->truck_number }}</td>
                                @foreach ($weeksByMonth[$current_month] as $week)
                                    @php

                                        // DB::enableQueryLog();
                                        if (isset($_GET['year']) && $_GET['year'] != "") {
                                            $result = DB::table('truck_dispatch')
                                                ->where('truck_id', $t->id)
                                                ->whereYear('created_on', $_GET['year'])
                                                ->where('created_on', '>=', $week['start'])
                                                ->where('created_on', '<=', date('Y-m-d', strtotime($week['end'])) . ' 23:59:59')
                                                ->first();
                                        } else {
                                            $result = DB::table('truck_dispatch')
                                                ->where('truck_id', $t->id)
                                                ->whereYear('created_on', $currentYear)
                                                ->where('created_on', '>=', $week['start'])
                                                ->where('created_on', '<=', date('Y-m-d', strtotime($week['end'])) . ' 23:59:59')
                                                ->first();
                                        }
                                        // dd(DB::getQueryLog());
                                        $dispatcher_name = DB::table('dispatchers')->where('id',@$result->dispatcher_id)->first();
                                    @endphp
                                    <td data-id="{{ @$result->id }}" data-status="{{ @$result->is_deleted }}"
                                        data-date="{{ $week['start'] }}" data-discription="{{ @$result->description }}"
                                        onclick="dispatch({{ $t->id }}, this)" style="cursor: pointer;"
                                        class="text-white @if (@$result->is_deleted == '0') bg-success @elseif(@$result->is_deleted == '1') bg-danger @else background-color @endif">
                                         {{ @$dispatcher_name->dispatcher_name? 'Dispatcher: '.@$dispatcher_name->dispatcher_name : '' }}<br>{{ @$result->description? 'Notes: '.@$result->description : '' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNewCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Add description here</h3>
                    </div>
                    <form method="POST" id="addNewCCForm" class="row g-3" action="">
                        @csrf
                        <div class="col-4">
                            <input type="hidden" name="dispatch_date" id="dispatch_date">
                            <input type="hidden" name="dispatch_id" id="dispatch_id" value="0">
                            <label for="">Truck</label>
                            <select class="form-select select2" name="truck" id="truck" required>
                                @foreach ($truck as $t)
                                    <option value="{{ $t->id }}">{{ $t->truck_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="">Dispatcher</label>
                            <select class="form-select select2" name="dispatcher_id" id="dispatcher_id" required>
                                @foreach ($dispatcher as $t)
                                    <option value="{{ $t->id }}">{{ $t->dispatcher_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="">Status</label>
                            <select class="form-select select2" name="dipatch_status" id="dipatch_status" required>
                                <option value="0">Dispatch</option>
                                <option value="1">Not Dispatch</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="4"></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Dispatch</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Function to load trucks based on the selected filter
        // function loadTrucks() {
        //     $.ajax({
        //         url: "{{ url('dispatchTrucks') }}",
        //         method: "POST",
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },

        //         success: function(response) {
        //             // Populate the table with data from the response
        //             $('#truckTable tbody').html(response);
        //         },
        //         error: function(xhr, status, error) {
        //             // Handle errors here
        //             console.error(error);
        //         }
        //     });
        // }

        function dispatch(id, clickedElement) {
            var dispatch_date = $(clickedElement).data('date');
            var dispatch_id = $(clickedElement).data('id');
            var discription = $(clickedElement).data('discription');
            var status = $(clickedElement).data('status');
            $('#addNewCCModal').modal('show')
            $('#truck').val(id)
            $('#description').val(discription)
            $('#dispatch_date').val(dispatch_date)
            $('#dipatch_status').val(status)
            if (dispatch_id) {
                $('#dispatch_id').val(dispatch_id)
            }
            $('#addNewCCForm').attr('action', '{{ url('dispatch/truck/') }}/' + id)
        }

        $(document).ready(function() {
            // Initial load of the 'Active' trucks
            // loadTrucks('active');

            // Filter buttons click event handlers
            // $('#activeBtn').click(function() {
            // loadTrucks();
            // });

            // $('#inactiveBtn').click(function() {
            //     loadTrucks('inactive');
            // });

            // $('#underDispatchBtn').click(function() {
            //     loadTrucks('under_dispatch');
            // });

            // $('#notUnderDispatchBtn').click(function() {
            //     loadTrucks('not_under_dispatch');
            // });

            // $('tbody').on('click', '.btn-change-status', function() {
            //     var id = $(this).attr('data-id');
            //     var status = $(this).attr('data-status');
            //     $.ajax({
            //         type: 'ajax',
            //         method: 'POST',
            //         data: {
            //             'id': id,
            //             'status': status
            //         },
            //         url: '{{ url('Change-Status') }}',
            //         success: function(res) {
            //             loadTrucks('active');
            //         }
            //     });
            // });
        });

        $('body').on('click', '.btn-dispatch', function() {
            var id = $(this).attr('data-id');
            console.log(id);
            $('#addNewCCModal').modal('show')
            $('#addNewCCForm').attr('action', '{{ url('dispatch/truck/') }}/' + id)
        })

        $('body').on('click', '.btn-return', function() {
            var id = $(this).attr('data-id');
            window.location = "{{ url('Return/truck/') }}/" + id;
        })

        $('#year').trigger('change');

        function updateMonths(selectedYear) {
            // Get the current year and month
            var currentYear = new Date().getFullYear();
            var selectedMonth = '{{ $current_month }}';
            var currentMonth = new Date().getMonth() + 1; // JavaScript months are 0-indexed

            // Default to the current year if no year is selected
            selectedYear = selectedYear || currentYear;

            // Clear existing options in the month dropdown
            $('#month').empty();

            // If the selected year is the current year, limit months to the current month
            var monthsCount = (selectedYear == currentYear) ? currentMonth : 12;

            // Append new options based on the logic
            for (var i = 1; i <= monthsCount; i++) {
                var displayMonth = i < 10 ? '0' + i : i;
                var optionText = new Date(selectedYear, i - 1, 1).toLocaleString('en-US', {
                    month: 'long'
                });
                var isSelected = (displayMonth == selectedMonth) ? 'selected' : '';

                $('#month').append('<option value="' + displayMonth + '" ' + isSelected + '>' + optionText + '</option>');
            }
        }
    </script>
@endsection
