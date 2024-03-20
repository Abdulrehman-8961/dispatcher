@extends('components/header')
@section('main')
<link rel="stylesheet" href="{{asset('public')}}/assets/vendor/libs/select2/select2.css" />
@php
    $trucks = DB::table('truck')->where('is_deleted', 0)->get();
    if(isset($_GET['truck_number']) && $_GET['truck_number'] != 0){
        $reports = DB::table('accident_report as ar')
    ->join('truck as t', 't.id', '=', 'ar.truck_id')
    ->where('t.is_deleted', 0)->where('ar.is_deleted', 0)
    ->select('ar.*', 't.truck_number')->where('t.id',$_GET['truck_number'])
    ->get();
    }else{
        $reports = DB::table('accident_report as ar')
    ->join('truck as t', 't.id', '=', 'ar.truck_id')
    ->where('t.is_deleted', 0)->where('ar.is_deleted', 0)
    ->select('ar.*', 't.truck_number')
    ->get();
    }
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Accident Reports</h4>
    <!-- List DataTable -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Create Report</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ url('SaveReport') }}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
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
                            <label for="truck_number">Truck #</label>
                            <select name="truck_number" id="truck_number" class="form-control select2" required>
                                <option value="">Choose Truck #</option>
                                @foreach ($trucks as $t)
                                    <option value="{{ $t->id }}" {{ old('truck_number') == $t->id ? 'selected' : '' }}>{{ $t->truck_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="report_file">PDF or Image</label>
                            <input type="file" name="report_files[]" id="report_file" class="form-control" multiple required>
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <div class="form-group text-center mt-2">
                            <button class="btn btn-primary" style="float-right"><i class="fa fa-save"></i>&nbsp;Save</button>
                        </div>
                    </div>
                </form>
                <form action="" method="GET">
                    <div class="row">
                        <div class="col-md-4 ">
                            <label for="truck_number">Truck #</label>
                            <select name="truck_number" id="truck_number" class="form-control " required>
                                <option value="0">Choose..</option>
                                @foreach ($trucks as $t)
                                    <option value="{{ $t->id }}" <?php if(isset($_GET['truck_number']) && $_GET['truck_number'] == $t->id) echo 'selected' ?>>{{ $t->truck_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group  mt-4">
                                <button class="btn btn-outline-primary">Filter</button>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Reports</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Truck #</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($reports) > 0)
                                    @foreach ($reports as $i)
                                        <tr>
                                            <td>{{ date('M-d-Y H:i:s', strtotime($i->created_on))}}</td>
                                            <td>{{$i->truck_number}}</td>
                                            <td><a href="{{url('download-files')}}/{{$i->id}}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i>&nbsp;View File</a></td>
                                            <td>
                                                <a href="{{url('Delete-Report')}}/{{$i->id}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?')"><i class="fa fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4"><p class="text-center">Nothing to display</p></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('public')}}/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{asset('public')}}/assets/vendor/libs/select2/select2.js"></script>
@endsection
