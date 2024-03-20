@extends('components/header')
@section('main')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Company</h4>
        @php
            if (isset($_GET)) {
                $sortBy = request('field');
                $sortOrder = request('orderBy');
                $perPage = 10;

                // Create the base query builder
                $company = DB::table('company')->where('is_deleted', 0);

                if (isset($sortBy)) {
                    $company->orderBy($sortBy, $sortOrder);
                }
                // Retrieve the results with pagination
                $company = $company->get();
            } else {
                // If there's no search, simply paginate all results
    $company = DB::table('company')
        ->where('is_deleted', 0)
        ->orderBy('id', 'asc')
                    ->get();
            }

        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Company List
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addcompany"
                            class="btn btn-outline-primary btn-sm" style="float: right;"><i class="fa fa-plus"></i> Add
                            Company</a>
                    </div>
                </div>
            </h5>

            <div class="table-responsive ">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($company as $o)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $o->company_name }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit-btn" data-id="{{ $o->id }}"
                                                data-name="{{ $o->company_name }}" href="javascript:;"><i
                                                    class="ti ti-pencil me-1"></i> Edit</a>
                                            <a class="dropdown-item"
                                                href="{{ url('Delete-company') }}/{{ $o->id }}"><i
                                                    class="ti ti-trash me-1"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->


    </div>


    <!-- Modal -->
    <div class="modal fade" id="addcompany" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Add New Company</h3>
                        {{-- <p class="text-muted">Add new Company</p> --}}
                    </div>
                    <form id="addNewCCForm" class="row g-3" method="POST" action="{{ url('save_company') }}">
                        @csrf

                        <div class="col-12 col-md-12">
                            <label class="form-label" for="company_name">Name <span class="imp_fields">*</span></label>
                            <input type="text" id="company_name" class="form-control" name="company_name"
                                placeholder="Company Name" required/>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editcompany" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Edit Company</h3>
                        {{-- <p class="text-muted">Add new Company</p> --}}
                    </div>
                    <form id="edit_company_form" class="row g-3" method="POST" action="">
                        @csrf
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="edit_company_name">Name <span class="imp_fields">*</span></label>
                            <input type="text" id="edit_company_name" class="form-control" name="company_name"
                                placeholder="Company Name"  required/>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                                aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('body').on('click', '.edit-btn', function() {
            var id = $(this).attr('data-id');
            var edit_name = $(this).attr('data-name');
            $('#edit_company_name').val(edit_name)
            $('#editcompany').modal('show');
            $('#edit_company_form').attr('action', '{{ url('update_company/') }}/' + id)
        })
    </script>
@endsection
