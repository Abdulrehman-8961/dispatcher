@extends('components/header')
@section('main')
    <link href="https://unpkg.com/filepond@4.28.2/dist/filepond.min.css" rel="stylesheet">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Documents /</span> Template Documents</h4>

        <!-- FilePond Upload Section -->
        <div class="mb-5">
            <h5>Upload Template Document</h5>
            <input type="file" class="filepond" id="documentUpload" name="documentUpload">
        </div>
        @php
            if (isset($_GET['name'])) {
                $docs = DB::table('documents')
                    ->where('orignal_name', 'LIKE', '%' . $_GET['name'] . '%')
                    ->where('is_deleted', 0)
                    ->get();
            } else {
                $docs = DB::table('documents')->where('is_deleted', 0)->get();
            }
        @endphp
        <!-- Template Documents List (if needed) -->
        <div class="row mb-3">
            <div class="col-md-4 offset-md-8">
                <form action="">
                    <div class="text-right">
                        <div class="d-flex">
                            <input type="text" class="form-control me-2" value="{{ old('name') }}" name="name"
                                id="name">
                            <button class="btn btn-outline-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            @foreach ($docs as $i)
                <div class="col-md-2 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="text-center">
                                @if ($i->type == 'video')
                                    <i class="fa fa-video fa-3x"></i>
                                @elseif($i->type == 'image')
                                <i class="fa fa-image fa-3x"></i>@else<i class="fa fa-file fa-3x"></i>
                                @endif
                            </h3>
                            <p class="text-center">{{ $i->orignal_name }}</p>
                        </div>
                        <div class="card-footer ">
                            <div class="row w-100">
                                <div class="col-6 p-0 m-0">
                                    <div class=" ">
                                        <a class="btn btn-primary btn-xs "
                                            href="{{ asset('public/uploads') }}/{{ $i->file }}"
                                            download="{{ $i->file }}"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="">
                                        <a class="btn btn-primary btn-xs  printButton"
                                            href="{{ url('uploads/delete') }}/{{ $i->id }}"
                                            onclick="return confirm('Are you sure you want to delete this?')"><i
                                                class="fa fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
                    url: '{{ url('Upload') }}', // Replace with the route to handle uploads
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
            $('.printButton').click(function() {
                var selectedDocumentId = $(this).attr('data-src');
                if (selectedDocumentId) {
                    // Handle document printing (if needed)
                }
            });
        });
    </script>
@endsection
