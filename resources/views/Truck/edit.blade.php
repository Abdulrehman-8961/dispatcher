@extends('components/header')
@section('main')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/select2/select2.css" />
    <style>
        /* Loader container */
        /* Loader container */
        .loader {
            border: 10px solid #f3f3f3;
            border-top: 10px solid #3498db;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 2s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -20px;
            /* Half of the height */
            margin-left: -20px;
            /* Half of the width */
        }

        /* Hide the loader by default */
        .loader.hidden {
            display: none;
        }

        /* Keyframes for the spinning animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Truck</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Edit Truck
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="{{ url('Truck') }}" class="btn btn-outline-primary btn-sm" style="float: right;"><i
                                class="fa fa-list"></i> &nbsp;Trucks List</a>
                    </div>
                </div>
            </h5>
            @php
                $owners = DB::table('owners')
                    ->where('is_deleted', 0)
                    ->select('id', 'company_name')
                    ->get();
            @endphp
            <div class="container">
                <form action="{{ url('updatetruck/' . $data->id) }}" method="POST" enctype="multipart/form-data"
                    id="saveTruckForm">
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
                                <select name="company" id="company" class=" form-select select2" required>
                                    <option value="">Choose company</option>
                                    @foreach ($owners as $o)
                                        <option value="{{ $o->id }}" <?php if ($o->id == $data->company_id) {
                                            echo 'selected';
                                        } ?>>{{ $o->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="d-none text-danger ms-2" id="company_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Truck Number <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->truck_number }}" name="truck_number"
                                    id="truck_number" class="form-control" value="{{ old('truck_number') }}"
                                    placeholder="Truck Number"  required>
                                    <span class="d-none text-danger ms-2" id="truck_number_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">VIN # <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->vin }}" name="vin" id="vin"
                                    class="form-control" value="{{ old('vin') }}" placeholder="VIN #" required>
                                    <span class="d-none text-danger ms-2" id="vin_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Year <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->year }}" name="year" id="year"
                                    class="form-control" value="{{ old('year') }}" placeholder="Year" required>
                                    <span class="d-none text-danger ms-2" id="year_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Make <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->make }}" name="make" id="make"
                                    class="form-control" value="{{ old('make') }}" placeholder="Make" required>
                                    <span class="d-none text-danger ms-2" id="make_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Model</label>
                                <input type="text" value="{{ $data->model }}" name="model" id="model"
                                    class="form-control" value="{{ old('model') }}" placeholder="Model" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">License Plate # <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->plate_number }}" name="license_plate"
                                    id="license_plate" class="form-control" value="{{ old('license_plate') }}"
                                    placeholder="License Plate #" required>
                                    <span class="d-none text-danger ms-2" id="license_plate_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Truck Address <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->truck_address }}" name="truck_address"
                                    id="truck_address" class="form-control" value="{{ old('truck_address') }}"
                                    placeholder="Truck Address" required>
                                    <span class="d-none text-danger ms-2" id="truck_address_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Trailer #</label>
                                <input type="text" value="{{ $data->trailer }}" name="trailer_number"
                                    id="trailer_number" class="form-control" value="{{ old('trailer_number') }}"
                                    placeholder="Trailer #">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Cab Card Renewal Date <span class="imp_fields">*</span></label>
                                <input type="date" value="{{ $data->card_renew_date }}" name="renew_date"
                                    id="renew_date" class="form-control" value="{{ old('renew_date') }}"
                                    placeholder="Cab Card Renewal Date" required>
                                    <span class="d-none text-danger ms-2" id="renew_date_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Dot Sticker Renewal Date <span class="imp_fields">*</span></label>
                                <input type="date" value="{{ $data->sticker_renew_date }}" name="dot_sticker_date"
                                    id="dot_sticker_date" class="form-control" value="{{ old('dot_sticker_date') }}"
                                    placeholder="Dot Sticker Renewal Date" required>
                                    <span class="d-none text-danger ms-2" id="dot_sticker_date_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage Insurance Name <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->damage_insurance_name }}"
                                    name="damage_insurance_date" id="damage_insurance_date" class="form-control"
                                    value="{{ old('damage_insurance_date') }}"
                                    placeholder="Physical Damage Insurance Name" required>
                                    <span class="d-none text-danger ms-2" id="damage_insurance_date_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage Insurance Policy Number <span class="imp_fields">*</span></label>
                                <input type="text" value="{{ $data->insurance_policy_number }}" name="policy_number"
                                    id="policy_number" class="form-control" value="{{ old('policy_number') }}"
                                    placeholder="Physical Damage Insurance Policy Number" required>
                                    <span class="d-none text-danger ms-2" id="policy_number_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage Effective Date <span class="imp_fields">*</span></label>
                                <input type="date" value="{{ $data->damage_effective_date }}" name="effective_Date"
                                    id="effective_Date" class="form-control" value="{{ old('effective_Date') }}"
                                    placeholder="Physical Damage Effective Date" required>
                                    <span class="d-none text-danger ms-2" id="effective_Date_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage Expiry Date <span class="imp_fields">*</span></label>
                                <input type="date" value="{{ $data->damage_expiry_date }}" name="damage_expiry"
                                    id="damage_expiry" class="form-control" value="{{ old('damage_expiry') }}"
                                    placeholder="Physical Damage Expiry Date" required>
                                    <span class="d-none text-danger ms-2" id="damage_expiry_warning" style="font-size: 12px">This field is required *</span>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Trailer Registration Renewal Date</label>
                                <input type="date" value="{{ $data->trailer_reg_renew_date }}" name="reg_renew"
                                    id="reg_renew" class="form-control" value="{{ old('reg_renew') }}"
                                    placeholder="Trailer Registration Renewal Date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Truck Leave Date</label>
                                <input type="date" value="{{ $data->truck_leave_date }}" name="truck_leave_date"
                                    id="truck_leave_date" class="form-control" value="{{ old('truck_leave_date') }}"
                                    placeholder="Truck Leave Date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">2290 Renewal Date <span class="imp_fields">*</span></label>
                                <input type="date" value="{{ $data->renewal_date_2290 }}" name="renewal_date_2290"
                                    id="renewal_date_2290" class="form-control" value="{{ old('renewal_date_2290') }}"
                                    placeholder="2290"  required>
                                    <span class="d-none text-danger ms-2" id="renewal_date_2290_warning" style="font-size: 12px">This field is required *</span>

                                    {{-- <input type="file" name="numbered_doc" id="numbered_doc" class="form-control" value="{{old('numbered_doc')}}" placeholder="2290"> --}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5><i class="fa fa-file"></i> &nbsp;Files Section</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="">2290 Renewal </label>
                                    <input type="file"  name="2290_document" id="2290_document" class="form-control">
                                </div>
                            </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Inspection </label>
                                <input type="file" name="inspection" id="inspection" class="form-control"
                                    value="{{ old('inspection') }}" placeholder="Inspection" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Cab Card </label>
                                <input type="file" name="cab_card" id="cab_card" class="form-control"
                                    value="{{ old('cab_card') }}" placeholder="Cab Card" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Truck Lease </label>
                                <input type="file" name="truck_lease" id="truck_lease" class="form-control"
                                    value="{{ old('truck_lease') }}" placeholder="Truck Lease" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage</label>
                                <input type="file" name="physical_damage" id="physical_damage" class="form-control"
                                    value="{{ old('physical_damage') }}" placeholder="Physical Damage">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Physical Damage Notice</label>
                                <input type="file" name="damage_notice" id="damage_notice" class="form-control"
                                    value="{{ old('damage_notice') }}" placeholder="Physical Damage Notice">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Trailer Registration (If Applicable)</label>
                                <input type="file" name="trailer_reg" id="trailer_reg" class="form-control"
                                    value="{{ old('trailer_reg') }}" placeholder="Trailer Registration (If Applicable)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">W9 </label>
                                <input type="file" name="w9" id="w9" class="form-control"
                                    value="{{ old('w9') }}" placeholder="W9" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">Saftey Report </label>
                                <input type="file" name="saftey_report" id="saftey_report" class="form-control"
                                    value="{{ old('saftey_report') }}" placeholder="Saftey Report">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="">4 Pictures of each side of truck</label>
                                <input type="file" name="truck_pics" id="truck_pics" class="form-control"
                                    placeholder="4 Pictures of each side of truck" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4" style="float: right;">
                                    <button class="btn btn-primary" id="saveTruckButton" type="button"><i
                                    class="fa fa-save"></i> &nbsp;Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->


    </div>
    <div id="loader" class="loader hidden"></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('public') }}/assets/vendor/libs/select2/select2.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>


$('#company').on('change',function(){
            validation()
        })

        $('#truck_number, #vin, #year, #make, #model, #truck_address, #license_plate, #renew_date, #dot_sticker_date, #policy_number, #damage_insurance_date, #damage_expiry, #renewal_date_2290, #effective_Date')
        .keyup(function() {
            validation();
        });

        $('#renew_date, #dot_sticker_date, #policy_number, #damage_insurance_date, #damage_expiry, #renewal_date_2290, #effective_Date')
        .change(function() {
            validation();
        });

        function validation(){
                var company=$('#company').val();
                var truck_number=$('#truck_number').val();
                var vin=$('#vin').val();
                var year=$('#year').val();
                var make=$('#make').val();
                var model=$('#model').val();
                var truck_address=$('#truck_address').val();
                var license_plate=$('#license_plate').val();
                var renew_date=$('#renew_date').val();
                var dot_sticker_date=$('#dot_sticker_date').val();
                var policy_number=$('#policy_number').val();
                var damage_insurance_date=$('#damage_insurance_date').val();
                var damage_expiry=$('#damage_expiry').val();
                var effective_Date=$('#effective_Date').val();
                var renewal_date_2290=$('#renewal_date_2290').val();

                if(company){ $('#company_warning').addClass('d-none') }else{ $('#company_warning').removeClass('d-none') }
                if(truck_number){ $('#truck_number_warning').addClass('d-none') }else{ $('#truck_number_warning').removeClass('d-none') }
                if(vin){ $('#vin_warning').addClass('d-none') }else{ $('#vin_warning').removeClass('d-none') }
                if(year){ $('#year_warning').addClass('d-none') }else{ $('#year_warning').removeClass('d-none') }
                if(make){ $('#make_warning').addClass('d-none') }else{ $('#make_warning').removeClass('d-none') }
                if(model){ $('#model_warning').addClass('d-none') }else{ $('#model_warning').removeClass('d-none') }
                if(license_plate){ $('#license_plate_warning').addClass('d-none') }else{ $('#license_plate_warning').removeClass('d-none') }
                if(truck_address){ $('#truck_address_warning').addClass('d-none') }else{ $('#truck_address_warning').removeClass('d-none') }
                if(renew_date){ $('#renew_date_warning').addClass('d-none') }else{ $('#renew_date_warning').removeClass('d-none') }
                if(dot_sticker_date){ $('#dot_sticker_date_warning').addClass('d-none') }else{ $('#dot_sticker_date_warning').removeClass('d-none') }
                if(damage_insurance_date){ $('#damage_insurance_date_warning').addClass('d-none') }else{ $('#damage_insurance_date_warning').removeClass('d-none') }
                if(policy_number){ $('#policy_number_warning').addClass('d-none') }else{ $('#policy_number_warning').removeClass('d-none') }
                if(effective_Date){ $('#effective_Date_warning').addClass('d-none') }else{ $('#effective_Date_warning').removeClass('d-none') }
                if(renewal_date_2290){ $('#renewal_date_2290_warning').addClass('d-none') }else{ $('#renewal_date_2290_warning').removeClass('d-none') }
                if(damage_expiry){ $('#damage_expiry_warning').addClass('d-none') }else{ $('#damage_expiry_warning').removeClass('d-none') }

        }


        $('.select2').select2();
        $(document).ready(function() {
            $('#saveTruckButton').on('click', function() {
                validation()
                var formData = new FormData(document.getElementById('saveTruckForm'));
                formData.delete('truck_pics');
                var truckPicsInput = document.getElementById('truck_pics');
                for (var i = 0; i < truckPicsInput.files.length; i++) {
                    formData.append('truck_pics[]', truckPicsInput.files[i]);
                }
                $('#loader').removeClass('hidden');
                $.ajax({
                    url: "{{ url('updatetruck/' . $data->id) }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        // Handle the success response here (e.g., show a success message)
                        $('.alert-success').html(response.message).removeClass('d-none');
                        $('#loader').addClass('hidden');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        var errorMessage = JSON.parse(xhr.responseText);
                        $('.alert-danger').html(errorMessage.message).removeClass('d-none');
                        $('#loader').addClass('hidden');
                    },
                    complete: function() {
                        // Remove the loader on both success and error
                        $('#loader').addClass('hidden');
                    }
                });
            });
        });
    </script>
@endsection
