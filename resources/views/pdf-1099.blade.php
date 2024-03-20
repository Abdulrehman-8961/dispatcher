<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif
        }
    </style>
</head>
@php
    $total = 0;
    $data = DB::table('truck')
        ->select('id')
        ->where('company_id', $owners)
        ->get();
    foreach ($data as $t_id) {
        $accounting = DB::table('truck_accounting')
            ->select('id')
            ->where(DB::raw('SUBSTRING(name, -4)'), $year)
            ->where('truck_id', $t_id->id)
            ->get();
        foreach ($accounting as $a) {
            $sum = DB::table('truck_income')
                ->select('amount')
                ->where('accounting_id', $a->id)
                ->where(DB::raw('YEAR(created_on)'), $year)
                ->where('category', 13)
                ->sum('amount');
            $total = $total + $sum;
        }
    }
@endphp

<body>
    <center>
        <span style="font-size: 20px">corrected(if checked)</span>
    </center>
    <div style="width:100%;height:110px;">
        <div style="width: 80%;float:left;">
            <div style="border:1px solid black;height:110px">
                <div style="width:60%;float:left;border-right:1px solid black;">
                    <span style="font-size: 10px;padding:5px">

                        {{ $payername }}, {{ $streetaddress }}, {{ $citytown }}, {{ $stateprorovince }},
                        {{ $country }}, {{ $zipcode }}
                    </span>
                </div>
                <div style="width:40%;float:right;">
                    <div style="width:55%;float:left;background:lightgray;height:110px">

                    </div>
                    <div style="width:45%;float:right;">
                        <p style="font-size: 10px;margin-top: 4px">OMB No. 1545-0116</p>
                        <p style="margin-bottom: 7px">
                            <span style="font-size: 10px">Form</span>
                            <span style="font-weight: 700;font-size:13px">1099-NEC</span>
                        </p>
                        <center><span style="font-size: 10px">(Rev. January 2022)</span></center>
                        <div style="border-bottom: 1px solid black"></div>
                        <center>
                            <span style="font-size: 9px">For calendar year</span>

                            <p style="font-size: 10px;margin-bottom:2px;margin-top:2px">20....</p>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <div style="width: 20%;float:right;">
            <h4>
                Nonemployee
                Compensation
            </h4>
        </div>

    </div>
    <br>
    <div style="width: 100%;margin-top:-18px;border:1px solid black;height:185px">
        <div style="width:80%;float:left">
            <div style="width: 100%;height:35px;border-bottom:1px solid black;">
                <div style="width: 50%;float:left;">
                    <div style="width: 100%">
                        <div style="width: 50%;float:left;border-right:1px solid black;height:35px;">
                            <span style="font-size: 10px;padding:3px">{{ $p_tin }}</span>
                        </div>
                        <div style="width: 50%;float:right;border-right:1px solid black;height:35px;">
                            <span style="font-size: 10px;padding:3px">{{ $r_tim }}</span>
                        </div>
                    </div>
                </div>
                <div style="width: 50%;float:right;border-right:1px solid black;height:35px;">
                    <span style="font-size: 8px;padding:3px"><b>1</b> Nonemployee compensation</span>
                    <p style="margin-top: 0px;margin-bottom: 0px;font-size:14px">${{ $total }}</p>
                </div>
            </div>
            <div style="width:100%;">
                <div style="width: 49.86%;float:left;height:150px;border-right:1px solid black;">
                    <p style="height: 30px;font-size:10px">
                        {{ $r_name }}

                    </p>
                    <p style="height: 20px;font-size:10px">
                        Street address (including apt. no.)
                    </p>
                    <p style="height: 20px;font-size:10px">
                        City or town, state or province, country, and ZIP or foreign postal code
                    </p>
                    <p style="height: 20px;font-size:10px">
                        Account number (see instructions)
                    </p>
                </div>
                <div style="width: 50%;float:right;height:150px;border-right:1px solid black">
                    <p style="height: 38px;font-size:8px;padding-left:3px;border-bottom:1px solid black;">
                        <b>2</b> Payer made direct sales totaling $5,000 or more of
                        consumer products to recipient for resale
                    </p>
                    <p style="height: 26px;font-size:10px;padding-left:3px;border-bottom:1px solid black;">
                        <b>3</b>
                    </p>
                    <p style="height: 26px;font-size:8px;padding-left:3px;border-bottom:1px solid black;">
                        <b>4</b> Federal income tax withheld
                        <span style="margin-top:0px;margin-bottom:0px ">$</span>
                    </p>
                    <div style="height: 21px;font-size:8px;padding-left:3px;">
                        <div style="width: 100%">
                            <div style="width: 50%;float:right;">
                                <b>6</b> State/Payer’s state no
                            </div>
                            <div style="width:50%;float: left;">
                                <b>5</b> State tax withheld
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="width:20%;float:right ">
            <div style="padding:3.5px;border-bottom:1px solid black">
                <span style="font-weight: 700;font-size:16px;"> Copy B For Recipient</span>
                <p style="margin:0px;font-size:10px">This is important tax
                    information and is being
                    furnished to the IRS. If you are
                    required to file a return, a
                    negligence penalty or other
                    sanction may be imposed on
                    you if this income is taxable
                    and the IRS determines that it
                    has not been reported.</p>
            </div>
            <div>
                <span style="font-size: 10px"><b>5</b> State tax withheld</span>
            </div>
        </div>

    </div>
    <div style="font-size: 10px">
        <span style="margin-right: 20px">Form 1099-NEC (Rev. 1-2022)</span>
        <span style="margin-right: 20px">(keep for your records)</span>
        <a style="margin-right: 20px;color:black" href="http://www.irs.gov/Form1099NEC">www.irs.gov/Form1099NEC</a>
        <span style="margin-right: 20px">Department of the Treasury - Internal Revenue Service</span>
    </div>
</body>

</html>
