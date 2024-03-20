@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Dispatchers</h4>
        @php
            $truckIds = DB::table('dispatchers')
                ->pluck('truck_id')
                ->toArray();

            // Explode each string into an array of integers
            $explodedTruckIds = array_map(function ($value) {
                return explode(',', $value);
            }, $truckIds);

            // Flatten the array to get a single array of integers
            $flattenedTruckIds = array_merge(...$explodedTruckIds);

            // Remove any null or empty values
            $cleanedTruckIds = array_filter($flattenedTruckIds);

            // Convert the values to integers
            $cleanedTruckIds = array_map('intval', $cleanedTruckIds);

            // Filter out duplicate values
            $cleanedTruckIds = array_unique($cleanedTruckIds);
            $trucks = DB::table('truck')
                ->where('is_deleted', 0)
                ->whereNotIn('id', $cleanedTruckIds)
                ->get();
            // dd($trucks);
        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Add Dispatcher
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="{{ url('Dispatcher') }}" class="btn btn-outline-primary btn-sm" style="float: right;"><i
                                class="fa fa-list"></i> &nbsp;Dispatchers List</a>
                    </div>
                </div>
            </h5>

            <div class="container">
                <form action="{{ url('SaveDispatcher') }}" method="POST" id="saveDispatcherForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <p class="alert alert-success d-none"></p>
                            <p class="alert alert-danger d-none"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Dispatcher Name <span class="imp_fields">*</span></label>
                                <input type="text" name="dispatcher_name" id="dispatcher_name"
                                    placeholder="Dispatcher Name" value="{{ old('dispatcher_name') }}" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selectpickerMultiple" class="form-label">Trucks <span class="imp_fields">*</span></label>
                            <select name="truck_id[]" id="truck_id" class="selectpicker w-100" data-style="btn-default"
                                multiple data-icon-base="ti" data-tick-icon="ti-check text-white" required>
                                @foreach ($trucks as $item)
                                    <option value="{{ $item->id }}">{{ $item->truck_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Phone <span class="imp_fields">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    value="{{ old('phone') }}" placeholder="Phone" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Email <span class="imp_fields">*</span></label>
                                <input type="text" name="email" id="email" class="form-control"
                                    value="{{ old('email') }}" placeholder="Email" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Percentage / Salary <span class="imp_fields">*</span></label>
                                <input type="text" name="salary" id="salary" class="form-control"
                                    value="{{ old('salary') }}" placeholder="Percentage / Salary" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Address <span class="imp_fields">*</span></label>
                                <input type="text" name="address" id="address" class="form-control"
                                    value="{{ old('address') }}" placeholder="Address" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Routing # <span class="imp_fields">*</span></label>
                                <input type="text" name="routing_number" id="routing_number" class="form-control"
                                    value="{{ old('routing_number') }}" placeholder="Routing #" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Account # <span class="imp_fields">*</span></label>
                                <input type="text" name="account_number" id="account_number" class="form-control"
                                    value="{{ old('account_number') }}" placeholder="Account #" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver Licenses # <span class="imp_fields">*</span></label>
                                <input type="text" name="driver_license_number" id="driver_license_number"
                                    class="form-control" value="{{ old('driver_license_number') }}"
                                    placeholder="Driver Licenses #" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">SSN # <span class="imp_fields">*</span></label>
                                <input type="text" name="ssn_number" id="ssn_number" class="form-control"
                                    value="{{ old('ssn_number') }}" placeholder="SSN #" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12">
                            <h5><i class="fa fa-file"></i> &nbsp;Files Section</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver Licenses <span class="imp_fields">*</span></label>
                                <input type="file" name="driver_licenses" id="driver_licenses" class="form-control"
                                    value="{{ old('driver_licenses') }}" placeholder="Driver Licenses" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">SSN <span class="imp_fields">*</span></label>
                                <input type="file" name="ssn_pic" id="ssn_pic" class="form-control"
                                    value="{{ old('ssn_pic') }}" placeholder="SSN" required>
                            </div>
                        </div>
                    </div>
                    <!-- Add more file inputs as needed -->

                    <div class="row mt-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4" style="float: right;">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> &nbsp;Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#saveDispatcherForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData($(this)[0]);

                // JavaScript validation, if needed
                $.ajax({
                    url: "{{ url('SaveDispatcher') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the success response here (e.g., show a success message)
                        $('.alert-success').html(response.message).removeClass('d-none');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        var errorMessage = JSON.parse(xhr.responseText);
                        $('.alert-danger').html(errorMessage.message).removeClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
