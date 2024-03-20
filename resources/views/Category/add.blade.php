@extends('components/header')
@section('main')
<link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Categories</h4>

    @php
        $categories = DB::table('categories')->where('is_deleted', 0)->get();
    @endphp
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Categories</h3>
            <div class="row mb-3">
                <div class="col-md-12">
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{url('Save-Category')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Category Name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form><hr>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $j = 0;
                                @endphp
                                @foreach ($categories as $i)
                                    <tr>
                                        <td>{{++$j}}</td>
                                        <td>{{$i->name}}</td>
                                        <td>
                                            <a href="{{url('Delete-Category')}}/{{$i->id}}" class="text-danger"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://unpkg.com/filepond@4.28.2/dist/filepond.min.js"></script>
<script>

    $(document).ready(function() {
        // Initialize FilePond
        FilePond.create(document.getElementById('documentUpload'), {
            acceptedFileTypes: ['image/*', 'application/pdf'],
            instantUpload: true,
            maxFiles: 10,
            allowDrop: true,
            allowPaste: true,
            allowMultiple: true,
            labelIdle: 'Drag & Drop your files or <span class="filepond--label-action"> Browse </span>',
            server: {
                url: '{{ url("Upload") }}', // Replace with the route to handle uploads
            }
        });

        // Upload completed event handler (if needed)
        FilePond.setOptions({
            onprocessfiles: function() {
               window.location.reload();
            }
        });

        // Download button click event handler (if needed)
        $('#downloadButton').click(function() {
            var selectedDocumentId = $('#selectDocument').val();
            if (selectedDocumentId) {
                // Handle document download (if needed)
            }
        });

        // Print button click event handler (if needed)
        $('#printButton').click(function() {
            var selectedDocumentId = $('#selectDocument').val();
            if (selectedDocumentId) {
                // Handle document printing (if needed)
            }
        });
    });
</script>
@endsection
