@extends('components/header')
@section('main')
@php
    $users = DB::table('users')->where('is_deleted', 0)->where('id', '!=', Auth::id())->paginate(10);
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Users</h4>
    <!-- List DataTable -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Users</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ url('Save-User') }}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
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
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <div class="form-group text-center mt-2">
                            <button class="btn btn-primary" style="float-right"><i class="fa fa-save"></i>&nbsp;Save</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Users</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($users) > 0)
                                    @foreach ($users as $i)
                                        <tr>
                                            <td>{{$i->name}}</td>
                                            <td>{{$i->email}}</td>
                                            <td>{{date('M-d-Y H:i:s', strtotime($i->created_at))}}</td>
                                            <td>
                                                <a href="{{url('Delete-User')}}/{{$i->id}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?')"><i class="fa fa-trash-alt"></i></a>
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
                        @if(count($users) > 0)
                        {{ @$users->appends(request()->input())->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
