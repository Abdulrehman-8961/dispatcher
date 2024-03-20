@extends('components/header')
@section('main')
@php
    $trucks = DB::table('truck')->where('is_deleted', 0)->get();
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Invoice /</span> Create</h4>
    <!-- List DataTable -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Create Invoice</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ url('SaveInvoice') }}" method="POST" class="form form-horizontal">
                    @csrf
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <p><strong>Oops, something went wrong</strong></p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="bol_number">BOL #</label>
                                <input type="text" name="bol_number" id="bol_number" class="form-control" placeholder="BOL #" value="{{ old('bol_number') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="pickup_address">Pickup Address</label>
                                <input type="text" name="pickup_address" id="pickup_address" class="form-control" placeholder="Pickup Address" value="{{ old('pickup_address') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="pickup_date">Pickup Date</label>
                                <input type="date" name="pickup_date" id="pickup_date" class="form-control" value="{{ old('pickup_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="dropoff_address">Dropoff Address</label>
                                <input type="text" name="dropoff_address" id="dropoff_address" class="form-control" placeholder="Dropoff Address" value="{{ old('dropoff_address') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="dropoff_date">Dropoff Date</label>
                                <input type="date" name="dropoff_date" id="dropoff_date" class="form-control" value="{{ old('dropoff_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="truck_number">Truck #</label>
                            <select name="truck_number" id="truck_number" class="form-control">
                                <option value="">Choose Truck #</option>
                                @foreach ($trucks as $t)
                                    <option value="{{ $t->id }}" {{ old('truck_number') == $t->id ? 'selected' : '' }}>{{ $t->truck_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount" value="{{ old('amount') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="exp_payment_date">Expected Payment Date</label>
                                <input type="date" name="exp_payment_date" id="exp_payment_date" class="form-control" value="{{ old('exp_payment_date') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <div class="form-group text-center mt-2">
                            <button class="btn btn-primary" style="float-right"><i class="fa fa-plus"></i>&nbsp;Create Invoice</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
