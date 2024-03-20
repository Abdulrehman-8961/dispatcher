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
                $company = DB::table('company_expense')->where('is_deleted', 0);

                if (isset($sortBy)) {
                    $company->orderBy($sortBy, $sortOrder);
                }
                // Retrieve the results with pagination
                $company = $company->paginate($perPage);
            } else {
                // If there's no search, simply paginate all results
    $company = DB::table('company_expense')
        ->where('is_deleted', 0)
        ->orderBy('id', 'asc')
                    ->paginate(10);
            }

        @endphp
        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Expense List
                    </div>
                    <div class="col-md-6 text-right float-right">
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addcompany"
                            class="btn btn-outline-primary btn-sm" style="float: right;"><i class="fa fa-plus"></i> Add
                            Expense</a>
                    </div>
                </div>
            </h5>

            <div class="table-responsive ">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Expense</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $rowCounter = ($company->currentPage() - 1) * $company->perPage() + 1;
                        @endphp
                        @foreach ($company as $o)
                            @php
                                $company__ = DB::table('company')
                                    ->where('id', $o->company_id)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $rowCounter++ }}</td>
                                <td>{{ $company__->company_name }}</td>
                                <td>{{ $o->expense_name }}</td>
                                <td>{{ $o->amount }}</td>
                                <td>{{ $o->date }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item edit-btn" data-id="{{ $o->id }}"
                                                data-c-name="{{ $o->company_id }}"data-e-name="{{ $o->expense_name }}"
                                                data-amount="{{ $o->amount }}" data-date="{{ $o->date }}"
                                                href="javascript:;"><i class="ti ti-pencil me-1"></i> Edit</a>
                                            <a class="dropdown-item"
                                                href="{{ url('Delete-expense') }}/{{ $o->id }}"><i
                                                    class="ti ti-trash me-1"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $company->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
        <!--/ Basic Bootstrap Table -->


    </div>
    @php
        $company_data = DB::table('company')
            ->where('is_deleted', 0)
            ->get();
    @endphp

    <!-- Modal -->
    <div class="modal fade" id="addcompany" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Add Expense</h3>
                    </div>
                    <form id="addNewCCForm" class="row g-3" method="POST" action="{{ url('save_expense') }}">
                        @csrf

                        <div class="col-12 col-md-12">
                            <label class="form-label" for="company_name">Company Name</label>
                            <select name="company_name" class="form-control" id="company_name">
                                @foreach ($company_data as $item)
                                    <option value="{{ $item->id }}">{{ $item->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="expense_name">Expense</label>
                            <input type="text" id="expense_name" required class="form-control" name="expense_name"
                                placeholder="Expense" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="amount">Amount</label>
                            <input type="number" id="amount" required class="form-control" name="amount"
                                placeholder="Amount" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="date">Date</label>
                            <input type="date" id="date" required class="form-control" name="date" />
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

    <div class="modal fade" id="editexpense" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Edit Expense</h3>
                    </div>
                    <form id="editexpenseform" class="row g-3" method="POST" action="">
                        @csrf

                        <div class="col-12 col-md-12">
                            <label class="form-label" for="edit_company_name">Company Name</label>
                            <select name="company_name" class="form-control" id="edit_company_name">
                                @foreach ($company_data as $item)
                                    <option value="{{ $item->id }}">{{ $item->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="edit_expense_name">Expense</label>
                            <input type="text" id="edit_expense_name" required class="form-control"
                                name="expense_name" placeholder="Expense" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="edit_amount">Amount</label>
                            <input type="number" id="edit_amount" required class="form-control" name="amount"
                                placeholder="Amount" />
                        </div>
                        <div class="col-12 col-md-12">
                            <label class="form-label" for="edit_date">Date</label>
                            <input type="date" id="edit_date" required class="form-control" name="date" />
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
            var edit_c_name = $(this).attr('data-c-name');
            var edit_e_name = $(this).attr('data-e-name');
            var edit_date = $(this).attr('data-date');
            var edit_amount = $(this).attr('data-amount');
            $('#edit_company_name').val(edit_c_name)
            $('#edit_expense_name').val(edit_e_name)
            $('#edit_date').val(edit_date)
            $('#edit_amount').val(edit_amount)
            $('#editexpense').modal('show');
            $('#editexpenseform').attr('action', '{{ url('update_expense/') }}/' + id)
        })
    </script>
@endsection
