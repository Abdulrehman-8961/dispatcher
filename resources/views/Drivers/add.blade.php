@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Drivers</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Add Driver
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="{{ url('Drivers') }}" class="btn btn-outline-primary btn-sm" style="float: right;"><i
                                class="fa fa-plus"></i> &nbsp;Drivers List</a>
                    </div>
                </div>
            </h5>
            @php
                $truck = DB::table('truck')
                    ->where('is_deleted', 0)
                    ->get();
            @endphp
            <div class="container">
                <form action="{{ url('SaveDriver') }}" method="POST" id="saveDriverForm">
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
                                <label for="">Driver Name <span class="imp_fields">*</span></label>
                                <input type="text" name="driver_name" id="driver_name" placeholder="Driver Name"
                                    value="{{ old('driver_name') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Truck Number <span class="imp_fields">*</span></label>
                                <select name="truck_id" class="form-select" id="truck_id" required>
                                    @foreach ($truck as $item)
                                        <option value="{{ $item->id }}">{{ $item->truck_number }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="truck_number" id="truck_number" class="form-control" value="{{old('truck_number')}}" placeholder="Truck Number"> --}}
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
                                <label for="">Phone <span class="imp_fields">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    value="{{ old('phone') }}" placeholder="Phone" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver License <span class="imp_fields">*</span></label>
                                <input type="text" name="license" id="license" class="form-control"
                                    value="{{ old('license') }}" placeholder="Driver License" required>
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
                                <label for="">Driver License Issue Date <span class="imp_fields">*</span></label>
                                <input type="date" name="license_issue_date" id="license_issue_date" class="form-control"
                                    value="{{ old('license_issue_date') }}" placeholder="Driver License Issue Date" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver License Expiry Date <span class="imp_fields">*</span></label>
                                <input type="date" name="license_expiry_date" id="license_expiry_date"
                                    class="form-control" value="{{ old('license_expiry_date') }}"
                                    placeholder="Driver License Expiry Date" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Medical Certificate Issue date <span class="imp_fields">*</span></label>
                                <input type="date" name="medical_issue_date" id="medical_issue_date" class="form-control"
                                    value="{{ old('medical_issue_date') }}" placeholder="Medical Certificate Issue date" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Medical Certificate Expiry Date <span class="imp_fields">*</span></label>
                                <input type="date" name="medical_expiry_date" id="medical_expiry_date"
                                    class="form-control" value="{{ old('medical_expiry_date') }}"
                                    placeholder="Medical Expiry Date" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">SSN # <span class="imp_fields">*</span></label>
                                <input type="text" name="ssn_number" id="ssn_number" class="form-control"
                                    value="{{ old('ssn_number') }}" placeholder="SSN #" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Hired Date <span class="imp_fields">*</span></label>
                                <input type="date" name="hired_date" id="hired_date" class="form-control"
                                    value="{{ old('hired_date') }}" placeholder="SSN #" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h5><i class="fa fa-file"></i> &nbsp;Files Section</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Medical Card <span class="imp_fields">*</span></label>
                                <input type="file" name="medical_card" id="medical_card" class="form-control"
                                    value="{{ old('medical_card') }}" placeholder="Medical Card" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Drug Test <span class="imp_fields">*</span></label>
                                <input type="file" name="drug_test" id="drug_test" class="form-control"
                                    value="{{ old('drug_test') }}" placeholder="Drug Test" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver License <span class="imp_fields">*</span></label>
                                <input type="file" name="driver_license_file" id="driver_license_file"
                                    class="form-control" value="{{ old('driver_license_file') }}"
                                    placeholder="Driver License" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">MVR <span class="imp_fields">*</span></label>
                                <input type="file" name="mvr" id="mvr" class="form-control"
                                    value="{{ old('mvr') }}" placeholder="MVR" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Driver Employment Application <span class="imp_fields">*</span></label>
                                <input type="file" name="employment_app" id="employment_app" class="form-control"
                                    value="{{ old('employment_app') }}" placeholder="Driver Employment Application" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Clearing House <span class="imp_fields">*</span></label>
                                <input type="file" name="clearing_house" id="clearing_house" class="form-control"
                                    value="{{ old('clearing_house') }}" placeholder="Clearing House"  required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Orientation <span class="imp_fields">*</span></label>
                                <input type="file" name="orentation" id="orentation" class="form-control"
                                    value="{{ old('orentation') }}" placeholder="Orientation" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Emergency Contact Form <span class="imp_fields">*</span></label>
                                <input type="file" name="emergency_contact" id="emergency_contact"
                                    class="form-control" value="{{ old('emergency_contact') }}"
                                    placeholder="Emergency Contact Form" required>
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
            $('#saveDriverForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData($(this)[0]);

                var licenseExpiryDate = formData.get('license_expiry_date');
                if (!licenseExpiryDate) {
                    // Handle the case where the field is empty
                    $('.alert-danger').html('Please provide a License Expiry Date').removeClass('d-none');
                    return;
                }

                $.ajax({
                    url: "{{ url('SaveDriver') }}",
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
