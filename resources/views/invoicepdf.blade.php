@php
$invoice_no = '';
    $data=DB::table('invoices')->where('id',request('id'))->first();
    if ($data->custom_invoice != null) {
        $invoice_no = $data->custom_invoice;
    } else {
        $invoice_no = $data->invoice_no;
    }
    $description=DB::table('invoice_description')->where('invoice_id',request('id'))->get();
    // dd($description);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        *{
            font-family: Helvetica;
        }
        #data {
            border-collapse: collapse;
            width: 100%;
            }

            #data td, #data th {
            border: 1px solid black;
            font-size:14px;
            padding: 3px
            }
            #customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 2px;
  font-size:14px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 2px;
  padding-bottom: 2px;
  text-align: left;
  background-color: #074064;
  color: white;
  font-size:14px;
}
    </style>
</head>
<body>
    <div style="width: 100%;height:50px;">
        <div style="width: 50%;float:left">
            <span style="font-size:26px;font-weight:700">AMERICAN TRANS LCC</span>
        </div>
        <div style="width: 50%;float:right;color:#074064">
            <span style="float: right">
                <span style="font-size:28px;font-weight:700">Invoice</span>
                <span style="font-size:35px">|</span>
            </span>
        </div>
    </div>

    <div style="width: 100%;margin-top:10px;height:240px">
        <div style="width: 50%;float:left;">
            <center>
                <div style="height:132px;">
                    <img style="height: 120px;width:350px" src="{{asset('public/assets')}}/pdf_logo.png">
                </div>
            </center>
            <div style="width:70%;background:#074064">
                <span style="color:white;margin-left:5px">Bill to:</span>
            </div>
            <p style="fony-size:12px;margin-top:2px;margin-bottom:2px">{{$data->cust_name}}</p>
            <p style="fony-size:12px;margin-top:2px;margin-bottom:2px">{{$data->cust_address_1}}</p>
            <p style="fony-size:12px;margin-top:2px;margin-bottom:2px">{{$data->cust_address_2}}</p>
            <p style="fony-size:12px;margin-top:2px;margin-bottom:2px">{{$data->cust_phone_no}}</p>
        </div>
        <div style="width: 40%;float:right;border:1px solid white;">
            <div style="width:100%;height:130px;">
                <div style="width: 50%;float:left">
                    <table>
                        <tbody>
                            <tr><td>Date :</td></tr>
                            <tr><td>Invoice #</td></tr>
                            <tr><td>Customer ID :</td></tr>
                            <tr><td>Bill of Lading :</td></tr>
                            <tr><td>Payment Due by :</td></tr>
                        </tbody>
                    </table>

                </div>
                <div style="width: 50%;float:right">
                    <table id="data">
                        <tbody>
                            <tr><td>{{$data->date}}</td></tr>
                            <tr><td>{{$invoice_no}}</td></tr>
                            <tr><td>{{$data->customer_id}}</td></tr>
                            <tr><td>{{$data->bill_of_landing}}</td></tr>
                            <tr><td>{{$data->due_date}}</td></tr>
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
    <table id="customers">
      <tr>
        <th style="width: 80%">Description</th>
        <th style="width: 20%;align:center">Amount</th>
      </tr>
      @foreach ($description as $item)
      <tr>
        <td >{{$item->description}}</td>
        <td >{{$item->amount}}</td>
      </tr>
      @endforeach
      </table>
     <div style="width:100%;font_size:15px;margin-top:5px;height:150px">
        <div style="width: 60%;float:left;border:1px solid black;height:150px">
            <div style="width:100%;background:#074064">
                <span style="color:white;margin-left:5px">Special Notes and instructions</span>
            </div>
            <span>
              {{$data->notes}}
            </span>
          </div>
          <div style="width: 35%;float:right">
            <div style="width: 50%;float:left">
                <p style="margin-top:6px;margin-bottom:6px">Subtotal</p>
                <p style="margin-top:6px;margin-bottom:6px">Sales Tax Rate</p>
                <p style="margin-top:6px;margin-bottom:6px">Sales Tax</p>
                <p style="margin-top:6px;margin-bottom:6px">S&H</p>
                <p style="margin-top:6px;margin-bottom:6px">Discount</p>
                <p style="margin-top:6px;margin-bottom:6px"><b>Total</b></p>
            </div>
            <div style="width: 50%;float:right">
                <p style="margin-top:5px;margin-bottom:5px;height:19px">
                    <span style="float: left">$</span>
                        <span style="float: right">{{$data->sub_total}}</span>
                </p>
                <p style="border:1px solid black;margin-top:5px;margin-bottom:5px;height:19px">
                    <span style="float:left">%</span>
                    <span style="float:right">{{$data->sales_tex_rate}}</span>
                </p>

                <p style="margin-top:5px;margin-bottom:5px;height:19px">
                    <span style="float:left">$</span>
                    <span style="float: right">{{$data->sales_tex}}</span>
                </p>

                <p style="border:1px solid black;margin-top:5px;margin-bottom:5px;height:19px">
                    <span style="float:left">$</span>
                    <span style="float: right">{{$data->s_h}}</span>
                </p>

                <p style="border:1px solid black;margin-top:5px;margin-bottom:5px;height:19px">
                    <span style="float: left">$</span>
                    <span style="float: right">{{$data->discount}}</span>
                </p>

                <p style="margin-top:5px;margin-bottom:5px;height:19px;font-weight:600">
                        <span style="float: left">$</span>
                        <span style="float: right">{{$data->total_amount}}</span>
                </p>
            </div>
          </div>
     </div>
      <div>
        <center>
            <p>Make all checks payable to AMERICAN TRANS LLC</p>
            <h4 style="margin-top:2px;margin-bottom:2px ">Thank you for your business!</h4>
            <p  style="margin-top:2px;margin-bottom:2px;font-size:14px ">Should you have any enquiries conceming this invoice, please contact Billing Department on 817-400-0989</p>
            <div style="width: 100%;border-top:3px dotted #074064"></div>
            <p  style="margin-top:2px;margin-bottom:2px ">2100 N HWY 360 SUITE # 300A,Grand Prane, Texas, 75050</p>
            <p  style="margin-top:2px;margin-bottom:2px ">Tell:817-400-0989 Fax: E-mail: americantrans18@gmail.com</p>
        </center>
      </div>
</body>
</html>
