@extends('components/header')
@section('main')
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">1099</h4>

        @php
            $owners = DB::table('owners')
                ->where('is_Deleted', 0)
                ->get();
            $result = DB::table('truck_income')
                ->select(DB::raw('YEAR(created_on) as year'), DB::raw('MAX(id) as id'))
                ->groupBy(DB::raw('YEAR(created_on)'))
                ->orderBy('year', 'DESC')
                ->get();

        @endphp
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">1099</h3>
                <div class="col-md-12 mb-3">
                    <form method="POST" id="pdfForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="">Payer's name</label>
                                <input name="payername" class="form-control" type="text" placeholder="Payer name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Street address</label>
                                <input name="streetaddress" class="form-control" type="text"
                                    placeholder="Street address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">City or town</label>
                                <input name="citytown" class="form-control" type="text" placeholder="City or town">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">State or province</label>
                                <input name="stateprorovince" class="form-control" type="text"
                                    placeholder="State or province">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Country</label>
                                <input name="country" class="form-control" type="text" placeholder="Country">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">ZIP or foregin postal code</label>
                                <input name="zipcode" class="form-control" type="text"
                                    placeholder="ZIP or foregin postal code">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Telephone No</label>
                                <input name="telephoneno" class="form-control" type="text" placeholder="Telephone No">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Payer's Tin</label>
                                <input name="p_tin" class="form-control" type="text" placeholder="Payer's Tin">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Recipient's Tin</label>
                                <input name="r_tim" class="form-control" type="text" placeholder="Recipient's Tin">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Recipient's Name</label>
                                <input name="r_name" class="form-control" type="text" placeholder="Recipient's Name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Owners</label>
                                <select name="owners" class="form-control" id="owners" required>
                                    @foreach ($owners as $o)
                                        <option value="{{ $o->id }}">{{ $o->owner_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="">Year</label>
                                <select name="year" class="form-control" id="year" required>
                                    @foreach ($result as $item)
                                        <option value="{{ $item->year }}"
                                            @if (@request('year') == $item->id) selected @endif>
                                            {{ $item->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mt-2">
                                <button type="button" id="generateBtn" class="btn btn-outline-primary">Generate</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    </div>
    <!-- Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <div class="d-flex">
                                <h5 class="mb-0">Nonemployee compensation: &nbsp;</h5>
                                <p class="mt-0 total_value"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#generateBtn').click(function() {
                // Serialize form data
                var formData = $('#pdfForm').serialize();
                var url = "{{ url('/1099/pdf') }}";
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(data) {
                        // Handle success (data contains the response from the server)
                        console.log(data);
                        var resultValue = data.result;
                        $('#resultModal').modal('show');
                        $('.total_value').html('$' + resultValue);

                    },
                    error: function(error) {
                        // Handle error
                        console.log(error);
                    }
                });
            });
        });
    </script>
@endsection
