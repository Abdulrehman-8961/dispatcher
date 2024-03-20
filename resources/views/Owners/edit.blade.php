@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Owners</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Edit Owner
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="{{ url('Owners') }}" class="btn btn-outline-primary btn-sm" style="float: right;"><i
                                class="fa fa-plus"></i> &nbsp;Owners List</a>
                    </div>
                </div>
            </h5>

            <div class="container">
                <form action="{{ url('editowner/' . $data->id) }}" method="POST">
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
                                <label for="">Company <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->company_name }}" name="company" id="company"
                                    class="form-control" value="{{ old('company') }}" placeholder="Comapny Name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Owner Name <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->owner_name }}" name="owner_name" id="owner_name"
                                    class="form-control" value="{{ old('owner_name') }}" placeholder="Owner Name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Address <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->address }}" name="address" id="address"
                                    class="form-control" value="{{ old('address') }}" placeholder="Address"  required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Phone Number <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->phone }}" name="phone_number" id="phone_number"
                                    class="form-control" value="{{ old('phone_number') }}" placeholder="Phone Number" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">EIN/SSN <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->ssn }}" name="ssn" id="ssn"
                                    class="form-control" value="{{ old('ssn') }}" placeholder="EIN/SSN" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Email <span class="imp_fields">*</span></label>
                                <input type="email" value="{{ $data->email }}" name="email" id="email"
                                    class="form-control" value="{{ old('email') }}" placeholder="Email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <h5><span class="fa fa-info-circle"></span> Account Payment Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="">Routing # <span class="imp_fields">*</span></label>
                                    <input type="text" value="{{ $data->routing }}" name="routing_number"
                                        id="routing_number" value="{{ old('routing_number') }}" placeholder="Routing #"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="">Account # <span class="imp_fields">*</span></label>
                                    <input type="text" value="{{ $data->account }}" name="account_number"
                                        id="account_number" value="{{ old('account_number') }}" placeholder="Account #"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <h5><span class="fa fa-file-alt"></span> Account Payment Information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="">Driver License</label>
                                    <input type="file" name="license" id="license" value="{{ old('license') }}"
                                        placeholder="Driver License" class="form-control">
                                </div>
                            </div>
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
            $('form').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url('editowner/' . $data->id) }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the success response here
                        $('.alert-success').html(response.message).removeClass('d-none');
                        // You can also redirect or display a success message to the user
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
