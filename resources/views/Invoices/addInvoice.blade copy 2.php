@extends('components/header')
@section('main')
    @php
        $invoice_num = DB::table('invoices')
            ->latest('created_on')
            ->first();
        if (isset($invoice_num)) {
            $lastInvoiceNo = $invoice_num->invoice_no;
            $invoiceNoParts = explode('-', $lastInvoiceNo);

            // Extract the last part and increment by 1
            $newCounter = intval($invoiceNoParts[1]) + 1;

            // Format the current year and new counter
            $currentYear = now()->format('Y');
            $newCounterFormatted = sprintf('%04d', $newCounter);

            // Create the new invoice number
            $invoice_no = "{$currentYear}-{$newCounterFormatted}-5558";
        } else {
            $invoice_no = '2023-0100-5558';
        }
    @endphp
    <style>
        #data,
        #data_1 {
            border-collapse: collapse;
            width: 100%;
        }

        #data td,
        #data th {
            border: 1px solid rgb(197, 178, 178);
            font-size: 14px;
            padding: 3px
        }

        #data_1 td,
        #data_1 th {
            font-size: 14px;
            padding: 3px
        }

        #items th {
            background-color: #074064;
            color: white;
            padding-top: 7px;
            padding-bottom: 7px;
        }
    </style>
    <form method="POST" action="{{ url('SaveInvoice') }}">
        @csrf
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Invoice /</span> Create</h4>
            <div class="row invoice-add">
                <!-- Invoice Add-->
                <div class="col-lg-12 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <p><strong>Oops, something went wrong</strong></p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error') }}
                                </div>
                            @endif
                            <div class="row m-sm-4 m-0">
                                <div class="col-md-6">
                                    <span style="font-weight:700;font-size:26px;color:black">AMERICAN TRANS LLC </span>
                                </div>
                                <div class="col-md-6">
                                    <span style="float: right;color:#074064">
                                        <span style="font-size:28px;font-weight:700">Invoice</span>
                                        <span style="font-size:35px">|</span>
                                    </span>
                                </div>
                            </div>
                            <div class="row m-sm-4 m-0 ">

                                <div class="col-md-7 mb-md-0 mb-4 ps-0 ">
                                    <div class="d-flex svg-illustration gap-2 align-items-center ">
                                        <div>
                                            <img style="width: 78%" src="{{ asset('public/assets') }}/pdf_logo.png">
                                        </div>
                                    </div>
                                    <div style="width:65%;background:#074064">
                                        <span style="color:white;margin-left:5px">Bill to:</span>
                                    </div>
                                    <div style="">
                                        <div class="row">
                                            <label class="col-4"
                                                style="font-size:14px;margin-top:2px;margin-bottom:2px">Customer:</label>
                                            <div class="col-8">
                                                <input required type="" name="cust_name" class="form-control"
                                                    style="border:none;height:25px" placeholder="Type here...">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-4" style="font-size:14px;margin-top:2px;margin-bottom:2px"
                                                for="address">Customer address:</label>
                                            <div class="col-8">
                                                <input required type="" name="cust_address_1" class="form-control"
                                                    style="border:none;height:25px" id="address"
                                                    placeholder="Type here...">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-4"
                                                style="font-size:14px;margin-top:2px;margin-bottom:2px">Customer
                                                address:</label>
                                            <div class="col-8">
                                                <input required type="" name="cust_address_2" class="form-control"
                                                    style="border:none;height:25px" placeholder="Type here...">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-4 pe-0"
                                                style="font-size:14px;margin-top:2px;margin-bottom:2px">Customer phone
                                                Number:</label>
                                            <div class="col-8">
                                                <input required type="" name="cust_no" class="form-control"
                                                    style="border:none;height:25px" placeholder="Type here...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row  mb-2">
                                        <div class="col-6">
                                            <table id="data_1">
                                                <tbody>
                                                    <tr>
                                                        <td>Date :</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Invoice #</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Customer ID :</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bill of Lading :</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Payment Due by :</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <table id="data">
                                                <tbody>
                                                    <tr>
                                                        <td><input name="date" required type="date"
                                                                class="form-control" style="border:none;height:20px"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input name="invoice_no" required readonly
                                                                value="{{ $invoice_no }}" class="form-control"
                                                                style="border:none;height:20px" placeholder="Invoice #">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input name="customer_id" required type=""
                                                                class="form-control" style="border:none;height:20px"
                                                                placeholder="Customer ID"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input name="bill_of_landing" required type=""
                                                                class="form-control" style="border:none;height:20px"
                                                                placeholder="Bill of Lading"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input name="due_date" required type="date"
                                                                class="form-control" style="border:none;height:20px"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div style="width:100%;background:#074064">
                                        <span style="color:white;margin-left:5px">Send Check to:</span>
                                    </div>
                                    <p style="font-size:14px;margin-top:3px;margin-bottom:3px">AMERICAN TRANS LLC</p>
                                    <p style="font-size:14px;margin-top:3px;margin-bottom:3px">2100 NHWY 360</p>
                                    <p style="font-size:14px;margin-top:3px;margin-bottom:3px">Grand Prarie, TX 75050</p>
                                    <p style="font-size:14px;margin-top:3px;margin-bottom:3px">817-400-0989</p>
                                </div>
                            </div>

                            <hr class="my-3 mx-n4" />
                            <div class="description ms-4 me-4">
                                <table id="items" class="table ">
                                    <thead>
                                        <tr>
                                            <th style="width: 80%">Description</th>
                                            <th style="width: 20%;">Amount</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="repeater">
                                        <tr>
                                            <td><input name="description[]" type="text" class="form-control"
                                                    style="border:none" placeholder="Enter Description"></td>
                                            <td><input name="amount[]" type="text"class="form-control d_amount"
                                                    style="border:none" placeholder="Enter Amount"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>

                                </table>
                                <button type="button" class="btn text-white mt-3 add-more btn-sm"
                                    style="background:#074064"><i class="fa fa-plus"></i> Add More</button>
                            </div>
                            <div class="row p-0 p-sm-4">
                                <div class="col-md-7 mb-md-0 mb-3">
                                    <label for="note" class="form-label fw-semibold text-white m-0 p-1 ps-2 w-100"
                                        style="background:#074064">Special Notes and instructions:</label>
                                    <textarea name="notes" class="form-control" style="border-radius:0px " rows="5" id="note"
                                        placeholder="Special Notes and instructions"></textarea>
                                </div>
                                <div class="col-md-5  d-flex justify-content-end">
                                    <div class="invoice-calculations">
                                        <div class="d-flex justify-content-between ">
                                            <span class="w-px-100">Subtotal:</span>
                                            <span class="fw-semibold">
                                                <span>$</span>
                                                <span id="subtotal">00.00</span>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between ">
                                            <span class="">Sales Tax Rate:</span>
                                            <input name="sales_tex_rate" id="sales_tex_rate" type="text"
                                                class="border h-75 ">
                                        </div>
                                        <div class="d-flex justify-content-between ">
                                            <span class="w-px-100">Sales Tax:</span>
                                            <span class="fw-semibold">
                                                <span>$</span>
                                                <span id="amount_after_sales">00.00</span>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between ">
                                            <span class="w-px-100">S&H:</span>
                                            <input name="s_h" type="text" id="s_h" class="border h-75 ">
                                        </div>
                                        <div class="d-flex justify-content-between ">
                                            <span class="w-px-100">Discount:</span>
                                            <input name="discount" type="text" id="discount" class="border h-75 ">
                                        </div>
                                        <hr />
                                        <div class="d-flex justify-content-between">
                                            <span class="w-px-100">Total:</span>
                                            <span class="fw-semibold">
                                                <span>$</span>
                                                <span id="full_total">00.00</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3 mx-n4" />

                            <div class="row px-0 px-sm-4">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <center>
                                            <p>Make all checks payable to AMERICAN TRANS LLC</p>
                                            <h4 style="margin-top:2px;margin-bottom:2px ">Thank you for your business!</h4>
                                            <p style="margin-top:2px;margin-bottom:2px;font-size:14px ">Should you have any
                                                enquiries conceming this invoice, please contact Billing Department on
                                                817-400-0989</p>
                                            <div style="width: 100%;border-top:3px dotted #074064"></div>
                                            <p style="margin-top:2px;margin-bottom:2px ">2100 N HWY 360 SUITE # 300A,Grand
                                                Prane, Texas, 75050</p>
                                            <p style="margin-top:2px;margin-bottom:2px ">Tell:817-400-0989 Fax: E-mail:
                                                americantrans18@gmail.com</p>
                                        </center>
                                    </div>
                                </div>
                            </div>
                            <button class="btn text-white" style="float-right;background:#074064;"><i
                                    class="fa fa-plus"></i>&nbsp;Create Invoice</button>
                        </div>
                    </div>
                </div>
                <!-- /Invoice Add-->
                <input type="hidden" name="total_amount" id="total_amount">
                <input type="hidden" name="sub_total_" id="sub_total_">
                <input type="hidden" name="sales_tex_" id="sales_tex_">
            </div>
        </div>
    </form>
    <script src="{{ asset('public') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script>
        $('.description').on('click', '.add-more', function() {
            var lastRow = $('.description tr:last');
            // var date = lastRow.find('.date').val();
            // var percent = lastRow.find('.percent_val').val();
            var html = `<tr>
            <td><input name="description[]"  type="text" class="form-control" style="border:none" placeholder="Enter Description"></td>
                        <td><input name="amount[]"  type="text"class="form-control d_amount" style="border:none" placeholder="Enter Amount"></td>
                                            <td><a href="javascript:;" class="remove" class="text-danger"><i class="fa fa-times"></i></a></td>
                                        </tr>`;
            $(this).parent().find('table').find('tbody').append(html);

        });

        $('#items').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            calculation();
        });
        $('body').on('keyup', '.d_amount', function() {
            var tr = $('.description').find('tbody').find('tr');
            tr.each(function() {
                var amount = parseFloat($(this).find('.d_amount').val());
                if (!isNaN(amount) && amount > 0) {} else {
                    $(this).find('.d_amount').val('')
                }
            });
            calculation();

        });
        $('body').on('keyup', '#s_h', function() {
            var val = $(this).val();
            if (!isNaN(val) && val >= 0) {} else {
                $(this).val('');
            }
            calculation();
        })
        $('body').on('keyup', '#discount', function() {
            var val = $(this).val();
            if (!isNaN(val) && val >= 0) {} else {
                $(this).val('');
            }
            calculation();
        })

        $('body').on('keyup', '#sales_tex_rate', function() {
            var val = $(this).val();
            if (!isNaN(val) && val >= 0) {} else {
                $(this).val('');
            }
            calculation();
        });

        function calculation() {
            var tr = $('.description').find('tbody').find('tr');
            var sum_amount = 0;

            tr.each(function() {
                var amount = parseFloat($(this).find('.d_amount').val()) || 0;
                sum_amount += amount;
            });
            var subtotal = parseFloat(sum_amount) || 0;

            $('#subtotal').text(subtotal);
            $('#sub_total_').val(subtotal);

            var sales_tex_rate = parseFloat($('#sales_tex_rate').val()) || 0;
            var amount_after_sales_tax = (subtotal / 100) * sales_tex_rate;
            $('#amount_after_sales').text(amount_after_sales_tax);
            $('#sales_tex_').val(amount_after_sales_tax);
            amount_after_sales_tax += subtotal;

            amount_after_sales_tax = parseFloat(amount_after_sales_tax) || 0;


            var s_h = parseFloat($('#s_h').val()) || 0;
            var discount = parseFloat($('#discount').val()) || 0;

            amount_after_sales_tax += s_h;
            amount_after_sales_tax -= discount;

            $('#full_total').text(amount_after_sales_tax);
            $('#total_amount').val(amount_after_sales_tax);


        }
    </script>
@endsection
