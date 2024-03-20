<!DOCTYPE html>
@php

    $truck_dispatched = DB::table('truck_dispatch')
        ->where('is_deleted', 0)
        ->get();
    foreach ($truck_dispatched as $td) {
        $givenDateTime = new DateTime($td->created_on);
        $givenDateTime->setTime(0, 0, 0);
        $currentDayOfWeek = $givenDateTime->format('w');
        $daysUntilNextSunday = 7 - $currentDayOfWeek;
        $nextSunday = $givenDateTime->add(new DateInterval("P{$daysUntilNextSunday}D"));
        $week_end = $nextSunday->format('Y-m-d');
        $today = date('Y-m-d');
        if ($today >= $week_end) {
            echo $td->truck_id;
            //DB::table('truck_dispatch')
              //  ->where('id', $td->id)
                //->update(['is_deleted' => 1]);


 $currentDate = new DateTime();
            $weekNumber = $currentDate->format('W');
            $yearNumber = $currentDate->format('o');
            $firstDay = new DateTime($yearNumber . '-01-01');
            $lastDay = new DateTime($yearNumber . '-12-31');
            $totalWeeks = 52;
            $name = $weekNumber . ' - ' . $totalWeeks . ' - ' . $yearNumber;

             $check=DB::table('cron_runs')->where('week',$name)->first();
             if($check==''){
             DB::table('cron_runs')->insert(['week'=>$name]);
            DB::table('truck_accounting')
                ->where('truck_id', $td->truck_id)
                ->update(['is_deleted' => 1]);
            $truck_accounting = DB::table('truck_accounting')
                ->where('truck_id', @$td->truck_id)
                ->first();


            $truck_expense_db = DB::table('truck_expense')
                ->where('accounting_id', @$truck_accounting->id)
                ->get();
            $total_expenses = 0;
            foreach ($truck_expense_db as $expense) {
                $total_expenses = $total_expenses + $expense->amount;
            }

            $truck_income_db = DB::table('truck_income')
                ->where('accounting_id', @$truck_accounting->id)
                ->get();
            $total_incomes = 0;
            foreach ($truck_income_db as $income) {
                $total_incomes = $total_incomes + $income->amount;
            }
            $net_income = $total_incomes - $total_expenses;
            $truck_accounting_id = DB::table('truck_accounting')->insertGetId([
                'truck_id' => $td->truck_id,
                'name' => $name,
            ]);
            if ($net_income < 0) {
                DB::table('truck_expense')->insert([
                    'accounting_id' => $truck_accounting_id,
                    'date' => date('Y-m-d'),
                    'description' => 'previous week expense',
                    'amount' => $net_income,
                ]);
            } else {
                DB::table('truck_income')->insert([
                    'date' => date('Y-m-d'),
                    'category' => '12',
                    'description' => 'previous week Income',
                    'amount' => $net_income,
                    'percent' => '3%',
                    'accounting_id' => $truck_accounting_id,
                ]);
            }
}        }
    }
@endphp
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('public') }}/assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dispatcher</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('public') }}/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/css/rtl/core.css"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/css/rtl/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/css/demo.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/select2/select2.css " />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('public') }}/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('public') }}/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/css/pages/cards-advance.css" />
    <!-- Helpers -->
    <script src="{{ asset('public') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('public') }}/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('public') }}/assets/js/config.js"></script>
    <link rel="stylesheet" href="{{ asset('public') }}/assets/vendor/libs/bootstrap-select/bootstrap-select.css" />

</head>
<style>
    .imp_fields {
        color: red;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ url('/') }}" class="app-brand-link">
                        <img src="{{ asset('public') }}/assets/logo.png" style="width: 40%;">
                        <span class="app-brand-text demo menu-text fw-bold">Epic Fleets</span>
                    </a>

                    {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
              <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a> --}}
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboards -->
                    <li class="menu-item">
                        <a href="{{ url('/') }}" class="menu-link">
                            <i class="menu-icon ti ti-home-2"></i>
                            <div data-i18n="Dashboards">Dashboards</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-certificate"></i>
                            <div data-i18n="Owners">Owners</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('Owners') }}" class="menu-link">
                                    <div data-i18n="View">View</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Owners/Add') }}" class="menu-link">
                                    <div data-i18n="Add">Add</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Old-Owners') }}" class="menu-link">
                                    <div data-i18n="Old Owners">Old Owners</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-truck-delivery"></i>
                            <div data-i18n="Truck">Truck</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('Truck') }}" class="menu-link">
                                    <div data-i18n="View">View</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Truck/Add') }}" class="menu-link">
                                    <div data-i18n="Add">Add</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Truck/Roster') }}" class="menu-link">
                                    <div data-i18n="Roster">Roster</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Truck-Dispatch') }}" class="menu-link">
                                    <div data-i18n="Dispatch">Dispatch</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Old-trucks') }}" class="menu-link">
                                    <div data-i18n="Old Trucks">Old Trucks</div>
                                </a>
                            </li>
                            {{-- <li class="menu-item">
                        <a href="{{url('Truck/Dispatch')}}" class="menu-link">
                            <div data-i18n="Dispatch">Dispatch</div>
                        </a>
                    </li> --}}

                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-steering-wheel"></i>
                            <div data-i18n="Drivers">Drivers</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('Drivers') }}" class="menu-link">
                                    <div data-i18n="View">View</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Drivers/Add') }}" class="menu-link">
                                    <div data-i18n="Add">Add</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="{{ url('Drivers/Roster') }}" class="menu-link">
                                    <div data-i18n="Roster">Roster</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Old-Drivers') }}" class="menu-link">
                                    <div data-i18n="Old Drivers">Old Drivers</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-transfer-in"></i>
                            <div data-i18n="Dispatcher">Dispatcher</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('Dispatcher') }}" class="menu-link">
                                    <div data-i18n="View">View</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Dispatcher/Add') }}" class="menu-link">
                                    <div data-i18n="Add">Add</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Dispatcher/Roster') }}" class="menu-link">
                                    <div data-i18n="Roster">Roster</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Old-dispatcher') }}" class="menu-link">
                                    <div data-i18n="Old Dispatchers">Old Dispatchers</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-building"></i>
                            <div data-i18n="Company">Company</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('View/Company') }}" class="menu-link">
                                    <div data-i18n="View">View</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Company/Expense') }}" class="menu-link">
                                    <div data-i18n="Expense">Expense</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Old-Company') }}" class="menu-link">
                                    <div data-i18n="Old Companys">Old Companys</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- <li class="menu-item">
                        <a href="{{ url('Truck/Dispatch') }}" class="menu-link">
                            <i class="menu-icon ti ti-truck-delivery"></i>
                            <div data-i18n="Dispatch">Dispatch</div>
                        </a>
                    </li> -->
                    <li class="menu-item">
                        <a href="{{ url('Truck/Accounting') }}" class="menu-link">
                            <i class="menu-icon ti ti-calculator"></i>
                            <div data-i18n="Truck Account">Truck Account</div>
                        </a>
                    </li>


                    <li class="menu-item">
                        <a href="{{ url('Documents') }}" class="menu-link">
                            <i class="menu-icon ti ti-file"></i>
                            <div data-i18n="Documents">Documents</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="{{ url('Upcomings') }}" class="menu-link">
                            <i class="menu-icon ti ti-calendar"></i>
                            <div data-i18n="Upcomings">Upcomings</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-receipt"></i>
                            <div data-i18n="Invoice">Invoice</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('Invoice/Create') }}" class="menu-link">
                                    <div data-i18n="Create">Create</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Invoice/Pending') }}" class="menu-link">
                                    <div data-i18n="Pending">Pending</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('Invoice/Paid') }}" class="menu-link">
                                    <div data-i18n="Paid">Paid</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('1099') }}" class="menu-link">
                            <i class="menu-icon ti ti-calendar"></i>
                            <div data-i18n="1099">1099</div>
                        </a>
                    </li>

                    {{-- <li class="menu-item">
                <a href="{{url('Truck/Accounting')}}" class="menu-link">
                    <i class="menu-icon ti ti-book"></i>
                    <div data-i18n="Truck Accounting">Truck Accounting</div>
                </a>
            </li> --}}

                    <li class="menu-item">
                        <a href="{{ url('Categories') }}" class="menu-link">
                            <i class="menu-icon ti ti-apps"></i>
                            <div data-i18n="Categories">Categories</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="{{ url('Dispatch-Statement') }}" class="menu-link">
                            <i class="menu-icon ti ti-book-2"></i>
                            <div data-i18n="Dispatch Statement">Dispatch Statement</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-calendar-plus"></i>
                            <div data-i18n="YTD">YTD</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('YTD') }}" class="menu-link">
                                    <div data-i18n="Overview">Overview</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('YTD/2') }}" class="menu-link">
                                    <div data-i18n="Company">Company</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon ti ti-calendar-plus"></i>
                            <div data-i18n="Escrow">Escrow</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="{{ url('escrow') }}" class="menu-link">
                                    <div data-i18n="Escrow">Escrow</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ url('escrow/return') }}" class="menu-link">
                                    <div data-i18n="Escrow return">Escrow return</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('Accident-Report') }}" class="menu-link">
                            <i class="menu-icon ti ti-calendar"></i>
                            <div data-i18n="Accident Report">Accident Report</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="{{ url('Users') }}" class="menu-link">
                            <i class="menu-icon ti ti-users"></i>
                            <div data-i18n="Users">Users</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('perc-settings') }}" class="menu-link">
                            <i class="menu-icon ti ti-settings"></i>
                            <div data-i18n="Perc Settings">Perc Settings</div>
                        </a>
                    </li>


                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        {{-- <div class="navbar-nav align-items-center">
                <div class="nav-item navbar-search-wrapper mb-0">
                  <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                  </a>
                </div>
              </div> --}}
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">

                            <!-- Style Switcher -->
                            <li class="nav-item me-2 me-xl-0">
                                <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                                    <i class="ti ti-md"></i>
                                </a>
                            </li>
                            <!--/ Style Switcher -->

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @if (Auth::user()->image == null)
                                            <img src="{{ asset('public') }}/assets/img/avatars/1.png" alt
                                                class="h-auto rounded-circle" />
                                        @else
                                            <img src="{{ asset('public') }}/uploads/{{ Auth::user()->image }}" alt
                                                class=" rounded-circle" />
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ url('Profile') }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        @if (Auth::user()->image == null)
                                                            <img src="{{ asset('public') }}/assets/img/avatars/1.png"
                                                                alt class="h-auto rounded-circle" />
                                                        @else
                                                            <img src="{{ asset('public') }}/uploads/{{ Auth::user()->image }}"
                                                                alt class=" rounded-circle" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                                                    <small class="text-muted">Admin</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('Profile') }}">
                                            <i class="ti ti-user-check me-2 ti-sm"></i>
                                            <span class="align-middle">Profile</span>
                                        </a>
                                    </li>
                                    {{-- <li>
                      <a class="dropdown-item" href="pages-account-settings-account.html">
                        <i class="ti ti-settings me-2 ti-sm"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="pages-account-settings-billing.html">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
                          <span class="flex-grow-1 align-middle">Billing</span>
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-label-danger w-px-20 h-px-20"
                            >2</span
                          >
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li> --}}
                                    {{-- <li>
                      <a class="dropdown-item" href="pages-help-center-landing.html">
                        <i class="ti ti-lifebuoy me-2 ti-sm"></i>
                        <span class="align-middle">Help</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="pages-faq.html">
                        <i class="ti ti-help me-2 ti-sm"></i>
                        <span class="align-middle">FAQ</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="pages-pricing.html">
                        <i class="ti ti-currency-dollar me-2 ti-sm"></i>
                        <span class="align-middle">Pricing</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li> --}}
                                    <li>
                                        <a class="dropdown-item" href="javascript:;"
                                            onclick="document.getElementById('logForm').submit()">
                                            <i class="ti ti-logout me-2 ti-sm"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST" id="logForm">@csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>

                    <!-- Search Small Screens -->
                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-xxl border-0"
                            placeholder="Search..." aria-label="Search..." />
                        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
                    </div>
                </nav>
                <div class="content-wrapper">
                    @yield('main')
                    @include('components/footer')
