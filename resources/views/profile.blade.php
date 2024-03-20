@extends('components/header')
@section('main')

<div class="container-xxl flex-grow-1 container-p-y">
                    <!-- List DataTable -->
                    <div class="col-md-6 offset-lg-3 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit user</h4>
                            </div>
                            <div class="card-body">
                            <form  action="{{ url('UpdateProfile') }}" enctype="multipart/form-data" method="POST" class="form form-horizontal">
                                    @csrf
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <p><strong>Opps Something went wrong</strong></p>
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
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="first-name">Name</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input style="display: none" name="id" value="{{Auth::id()}}" >
                                                    <input required type="text" id="first-name" class="form-control" name="name" placeholder="Name" value="{{Auth::user()->name}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="email-id">Email</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input required type="email" id="email-id" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }} " {{ $errors->has('email') ? 'autofocus' : '' }}  name="email" placeholder="Email" value="{{Auth::user()->email}}">
                                                    @if ($errors->has('email'))
                                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="image">Image</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input  type="file" id="image" class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }} " {{ $errors->has('image') ? 'autofocus' : '' }}  name="image" placeholder="image">
                                                    @if ($errors->has('image'))
                                                        <span class="text-danger">{{ $errors->first('image') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="password">Password</label>
                                                    <small><code>(Leave empty if don't want to change)</code></small>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input  type="text" id="password" class="form-control" name="password" placeholder="Password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Update</button>
                                            <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

</div>
@endsection
