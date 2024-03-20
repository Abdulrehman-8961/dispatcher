@extends('components/header')
@section('main')
    @php
        // $invoices = DB::table('invoices as i')->join('truck as t', 't.id', '=', 'i.truck_number')->where('i.is_deleted', 0)->where('t.is_deleted', 0)->where('i.status', 'paid')->select('i.*', 't.truck_number')->get();
        $invoices = DB::table('invoices')
            ->where('is_deleted', 0)
            ->where('status', 'paid')
            ->get();
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Invoice /</span> Paid</h4>
        <!-- List DataTable -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Paid Invoices</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group text-right" style="float: right">
                                <a href="{{ url('Invoice/Create') }}" class="btn btn-outline-primary btn-sm"><i
                                        class="fa fa-plus"></i>&nbsp;Add Invoice</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Due date</th>
                                    <th class="text-center">Invoice #</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Customer Number</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($invoices) > 0)
                                    @foreach ($invoices as $i)
                                        @php
                                        $invoice_no = '';
                                            if ($i->custom_invoice != null) {
                                                $invoice_no = $i->custom_invoice;
                                            } else {
                                                $invoice_no = $i->invoice_no;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ date('M-d-Y', strtotime($i->date)) }}</td>
                                            <td class="text-center">{{ date('M-d-Y', strtotime($i->due_date)) }}</td>
                                            <td class="text-center">{{ $invoice_no }}</td>
                                            <td class="text-center">{{ $i->cust_name }}</td>
                                            <td class="text-center">{{ $i->cust_phone_no }}</td>
                                            <td class="text-center">{{ $i->total_amount }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ url('print-Invoice') }}/{{ $i->id }}"
                                                        class="btn btn-sm btn-primary me-1"><i
                                                            class="ti ti-printer"></i></a>
                                                    <button data-id="{{ $i->id }}"
                                                        data-invoice="{{ $invoice_no }}"
                                                        class="btn btn-sm btn-info send-mail"><i
                                                            class="ti ti-mail"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="addNewCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Add email here</h3>
                    </div>
                    <form method="POST" id="addNewCCForm" class="row g-3" action="">
                        @csrf
                        <input type="hidden" name="invoice_no" id="invoice_no">
                        <div class="col-12">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Send Email</button>
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
        $(document).ready(function() {
            $('.send-mail').on('click', function() {
                var id = $(this).data('id');
                var invoice_no = $(this).data('invoice');
                if (id && invoice_no) {
                    $('#addNewCCModal').modal('show');
                    $('#invoice_no').val(invoice_no);
                    $('#addNewCCForm').attr('action', '{{ url('sendInvoicePDF/') }}/' + id);
                }
            })
        });
    </script>
@endsection
