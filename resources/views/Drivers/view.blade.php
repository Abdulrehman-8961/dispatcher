@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Drivers</h4>
    @php
    if(isset($_GET)){
    $sortBy = request('field');
    $sortOrder = request('orderBy');
    $start = request('start_date');
    $end = request('end_date');
    $search = @$_GET['search'];
    $perPage = 100;

    // Create the base query builder
    $drivers = DB::table('drivers')->where('is_deleted', 0);

    if($search){
        $drivers->where(function($query) use($search){
            $query->orWhere('driver_name', 'like', '%'.@$search.'%');
            $query->orWhere('truck_number', 'like', '%'.@$search.'%');
            $query->orWhere('email', 'like', '%'.@$search.'%');
            $query->orWhere('phone', 'like', '%'.@$search.'%');
            $query->orWhere('address', 'like', '%'.@$search.'%');
            $query->orWhere('driver_license', 'like', '%'.@$search.'%');
        });
    }

    if($start || $end){
        $drivers->where(function($qry) use($start, $end){
            if($start){
                $qry->where('created_on', '>=', @$start.' 00:00:01');
            }
            if($end){
                $qry->where('created_on', '<=', @$end.' 23:59:00');
            }
        });
    }

    if(isset($sortBy)){
        $drivers->orderBy($sortBy, $sortOrder);
    }

    // Retrieve the results with pagination
    $drivers = $drivers->get();
} else {
    // If there's no search, simply paginate all results
    $drivers = DB::table('drivers')->where('is_deleted', 0)
        ->orderBy('id', 'asc')
        ->get();
}

    @endphp
    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">
        <div class="row">
            <div class="col-md-6">
                Drivers List
            </div>
            <div class="col-md-6 text-right float-right">
                <a href="{{url('Drivers/Add')}}" class="btn btn-outline-primary btn-sm" style="float: right;"><i class="fa fa-plus"></i> Add Driver</a>
            </div>
        </div>
      </h5>

      <form>
        <div class="row">
          <div class="col-md-3 offset-1">
              <input type="date" name="start_date" class="form-control" value="{{@$_GET['start_date']}}">
          </div>
          <div class="col-md-3">
              <input type="date" name="end_date" class="form-control" value="{{@$_GET['end_date']}}">
          </div>
          <div class="col-md-3">
              <input type="text" name="search" class="form-control" value="{{@$_GET['search']}}" placeholder="Search Anything...">
          </div>
          <input type="hidden" name="orderBy" value="{{ request('orderBy') }}">
          <input type="hidden" name="field" value="{{ request('field') }}">
          <div class="col-md-2">
              <button class="btn btn-outline-primary">Search</button>
          </div>
        </div>
        <hr>
        </form>

      <div class="table-responsive ">
        <table class="table mb-5">
          <thead>
            <tr>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=id">#</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=driver_name">Driver Name</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=truck_id">Truck Number</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=email">Email</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=phone">Phone</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=driver_license">License</a></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach ($drivers as $o)
            @php
                $truck=DB::table('truck')->where('id',$o->truck_id)->first();
            @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $o->driver_name }}</td>
                    <td>{{ @$truck->truck_number }}</td>
                    <td>{{ $o->email }}</td>
                    <td>{{ $o->phone }}</td>
                    <td>{{ $o->driver_license }}</td>
                    <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{url('Edit-Driver')}}/{{$o->id}}"
                              ><i class="ti ti-pencil me-1"></i> Edit</a
                            >
                            <a class="dropdown-item" href="{{url('Delete-Driver')}}/{{$o->id}}"
                              ><i class="ti ti-trash me-1"></i> Delete</a
                            >
                            <a class="dropdown-item driver-detail" href="javascript:void(0);" data-id="{{$o->id}}"
                              ><i class="ti ti-info-circle me-1"></i> Details</a
                            >
                            <a class="dropdown-item" href="{{url('archive-driver')}}/{{$o->id}}"
                              ><i class="ti ti-archive me-1"></i> Archived Doc</a
                            >
                          </div>
                        </div>
                      </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{-- {{ $drivers->appends(request()->input())->links() }} --}}
      {{-- {{ $drivers->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
    </div>
    <!--/ Basic Bootstrap Table -->


  </div>
 <!-- Modal -->
 <div class="modal fade" id="ownerdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Driver Detail</h5>
          <button type="button" class="close btn btn-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="width: 100%" id="details">
        </div>
      </div>
    </div>
  </div>

  <!--Truck Details Modal -->
  <div class="modal fade" id="truckdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle">Truck Detail</h5>
              <button type="button" class="close btn truck-btn-close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body" id="truck_details">
          </div>
          <div class="modal-footer d-flex justify-content-start">
            <button class="btn btn-outline-primary btn-sm back-btn">Back</button>
          </div>
      </div>
  </div>
</div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>

    $('.btn-close').on('click', function(){
        $('#ownerdetailModal').modal('toggle');
    })
    $('.truck-btn-close').on('click', function(){
        $('#truckdetailModal').modal('toggle');
    })
    $('.back-btn').on('click', function(){
        $('#truckdetailModal').modal('toggle');
        $('#ownerdetailModal').modal('toggle');
    })
    $('tbody').on('click', '.driver-detail', function(){
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'ajax',
            method: 'POST',
            data: {'id': id},
            url: "{{url('getDriverDetails')}}",
            success: function(res){
                console.log(res);
                if(res){

                    var medical_card='';
                  if(res.medical_card != null){
                    var link_medical_card = "{{asset('public/uploads')}}/"+res.medical_card;
                    var fileNameParts = res.medical_card.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      medical_card='<a href="'+link_medical_card+'" download><img src="'+link_medical_card+'" class="img w-100"/></a>';
                    }else{
                      medical_card= '<a href="'+link_medical_card+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }

                    var clearing_house='';
                  if(res.clearing_house != null){
                    var link_clearing_house = "{{asset('public/uploads')}}/"+res.clearing_house;
                    var fileNameParts = res.clearing_house.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      clearing_house='<a href="'+link_clearing_house+'" download><img src="'+link_clearing_house+'" class="img w-100"/></a>';
                    }else{
                      clearing_house= '<a href="'+link_clearing_house+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }



                    // var drug_test = "{{asset('public/uploads')}}/"+res.drug_test;
                    var drug_test='';
                  if(res.drug_test != null){
                    var link_drug_test = "{{asset('public/uploads')}}/"+res.drug_test;
                    var fileNameParts = res.drug_test.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      drug_test='<a href="'+link_drug_test+'" download><img src="'+link_drug_test+'" class="img w-100"/></a>';
                    }else{
                      drug_test= '<a href="'+link_drug_test+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var license = "{{asset('public/uploads')}}/"+res.license;
                    var license='';
                  if(res.license != null){
                    var link_license = "{{asset('public/uploads')}}/"+res.license;
                    var fileNameParts = res.license.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      license='<a href="'+link_license+'" download><img src="'+link_license+'" class="img w-100"/></a>';
                    }else{
                      license= '<a href="'+link_license+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var mvr = "{{asset('public/uploads')}}/"+res.mvr;
                    var mvr='';
                  if(res.mvr != null){
                    var link_mvr = "{{asset('public/uploads')}}/"+res.mvr;
                    var fileNameParts = res.mvr.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      mvr='<a href="'+link_mvr+'" download><img src="'+link_mvr+'" class="img w-100"/></a>';
                    }else{
                      mvr= '<a href="'+link_mvr+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var employment_application = "{{asset('public/uploads')}}/"+res.employment_application;
                    var employment_application='';
                  if(res.employment_application != null){
                    var link_employment_application = "{{asset('public/uploads')}}/"+res.employment_application;
                    var fileNameParts = res.employment_application.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      employment_application='<a href="'+link_employment_application+'" download><img src="'+link_employment_application+'" class="img w-100"/></a>';
                    }else{
                      employment_application= '<a href="'+link_employment_application+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var orientation = "{{asset('public/uploads')}}/"+res.orientation;
                    var orientation='';
                  if(res.orientation != null){
                    var link_orientation = "{{asset('public/uploads')}}/"+res.orientation;
                    var fileNameParts = res.orientation.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      orientation='<a href="'+link_orientation+'" download><img src="'+link_orientation+'" class="img w-100"/></a>';
                    }else{
                      orientation= '<a href="'+link_orientation+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var emergency_contact = "{{asset('public/uploads')}}/"+res.emergency_contact;
                    var emergency_contact='';
                  if(res.emergency_contact != null){
                    var link_emergency_contact = "{{asset('public/uploads')}}/"+res.emergency_contact;
                    var fileNameParts = res.emergency_contact.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      emergency_contact='<a href="'+link_emergency_contact+'" download><img src="'+link_emergency_contact+'" class="img w-100"/></a>';
                    }else{
                      emergency_contact= '<a href="'+link_emergency_contact+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var ssn_file = "{{asset('public/uploads')}}/"+res.ssn_file;
                    var ssn_file='';
                  if(res.ssn_file != null){
                    var link_ssn_file = "{{asset('public/uploads')}}/"+res.ssn_file;
                    var fileNameParts = res.ssn_file.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      ssn_file='<a href="'+link_ssn_file+'" download><img src="'+link_ssn_file+'" class="img w-100"/></a>';
                    }else{
                      ssn_file= '<a href="'+link_ssn_file+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                  if(res.truck_number != null){
                    truck_details = '<a class="dropdown-item truck-detail" href="javascript:void(0);"data-id="'+res.truck_id+'">'+res.truck_number+'</a>'
                    } else {
                        truck_details = '';
                    }
                    function formatDate(inputDate) {
                      var parsedDate = new Date(inputDate);
                      var formattedDate = (parsedDate.getMonth() + 1).toString().padStart(2, '0') + '-' +
                                          parsedDate.getDate().toString().padStart(2, '0') + '-' +
                                          parsedDate.getFullYear();
                                          return formattedDate;
                  }
                    $('#ownerdetailModal').modal('toggle');
                    $('#details').html(
                        `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Driver Name</th>
                                        <th>Truck Number</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>License</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>`+res.driver_name+`</td>
                                        <td>`+truck_details+`</td>
                                        <td>`+res.phone+`</td>
                                        <td>`+res.email+`</td>
                                        <td>`+res.driver_license+`</td>
                                        <td>`+res.created_on+`</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5>Address</h5><p>`+res.address+`</p></div>
                            <div class="col-md-6"><h5>License Issue Date</h5><p>`+formatDate(res.license_issue_date)+`</p></div>
                            <div class="col-md-6"><h5>License Expiry Date</h5><p>`+formatDate(res.license_expiry_date)+`</p></div>
                            <div class="col-md-6"><h5>Medical Issue Date</h5><p>`+formatDate(res.medical_issue_date)+`</p></div>
                            <div class="col-md-6"><h5>Medical Expiry Date</h5><p>`+formatDate(res.medical_expiry_date)+`</p></div>
                            <div class="col-md-6"><h5>SSN #</h5><p>`+res.ssn+`</p></div>
                            <div class="col-md-12"><hr><h5>Medical Card:</h5>`+medical_card+`</div>
                            <div class="col-md-12"><h5>Drug Test:</h5>`+drug_test+`</div>
                            <div class="col-md-12"><h5>License:</h5>`+license+`</div>
                            <div class="col-md-12"><h5>MVR:</h5>`+mvr+`</div>
                            <div class="col-md-12"><h5>Employment Application:</h5>`+employment_application+`</div>
                            <div class="col-md-12"><h5>Clearing House:</h5>`+clearing_house+`</div>
                            <div class="col-md-12"><h5>Orientation:</h5>`+orientation+`</div>
                            <div class="col-md-12"><h5>Emerygency Contact:</h5>`+emergency_contact+`</div>
                            <div class="col-md-12"><h5>SSN:</h5>`+ssn_file+`</div>
                        </div>
                        `


                    );
                }
            }
        });
    });

    $(document).on('click', '.truck-detail', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'ajax',
                method: 'POST',
                data: {
                    'id': id
                },
                url: "{{ url('getTruckDetails') }}",
                success: function(res) {
                    if (res) {
                        var inspection = '';
                        if (res.inspection != null) {
                            var link_inspection = "{{ asset('public/uploads') }}/" + res.inspection;
                            var fileNameParts = res.inspection.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                inspection = '<a href="' + link_inspection + '" download><img src="' +
                                    link_inspection + '" class="img w-100"/></a>';
                            } else {
                                inspection = '<a href="' + link_inspection +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var car_cab = '';
                        if (res.car_cab != null) {
                            var link_car_cab = "{{ asset('public/uploads') }}/" + res.car_cab;
                            var fileNameParts = res.car_cab.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                car_cab = '<a href="' + link_car_cab + '" download><img src="' +
                                    link_car_cab + '" class="img w-100"/></a>';
                            } else {
                                car_cab = '<a href="' + link_car_cab +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var report_2290 = '';
                        console.log(res.document_2290);
                        if (res.document_2290 != null) {
                            var link_document_2290 = "{{ asset('public/uploads') }}/" + res
                                .document_2290;
                            var fileNameParts = res.document_2290.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                report_2290 = '<a href="' + link_document_2290 +
                                    '" download><img src="' + link_document_2290 +
                                    '" class="img w-100"/></a>';
                            } else {
                                report_2290 = '<a href="' + link_document_2290 +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var truck_lease = '';
                        if (res.truck_lease != null) {
                            var link_truck_lease = "{{ asset('public/uploads') }}/" + res.truck_lease;
                            var fileNameParts = res.truck_lease.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                truck_lease = '<a href="' + link_truck_lease + '" download><img src="' +
                                    link_truck_lease + '" class="img w-100"/></a>';
                            } else {
                                truck_lease = '<a href="' + link_truck_lease +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var physical_damage = '';
                        if (res.physical_damage != null) {

                            var link_physical_damage = "{{ asset('public/uploads') }}/" + res
                                .physical_damage;
                            var fileNameParts = res.physical_damage.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                physical_damage = '<a href="' + link_physical_damage +
                                    '" download><img src="' + link_physical_damage +
                                    '" class="img w-100"/></a>';
                            } else {
                                physical_damage = '<a href="' + link_physical_damage +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var physical_notice = '';
                        if (res.physical_notice != null) {

                            var link_physical_notice = "{{ asset('public/uploads') }}/" + res
                                .physical_notice;
                            var fileNameParts = res.physical_notice.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                physical_notice = '<a href="' + link_physical_notice +
                                    '" download><img src="' + link_physical_notice +
                                    '" class="img w-100"/></a>';
                            } else {
                                physical_notice = '<a href="' + link_physical_notice +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var trailer_reg = '';
                        if (res.trailer_reg != null) {

                            var link_trailer_reg = "{{ asset('public/uploads') }}/" + res.trailer_reg;
                            var fileNameParts = res.trailer_reg.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                trailer_reg = '<a href="' + link_trailer_reg + '" download><img src="' +
                                    link_trailer_reg + '" class="img w-100"/></a>';
                            } else {
                                trailer_reg = '<a href="' + link_trailer_reg +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var w9 = '';
                        if (res.w9 != null) {
                            var link_w9 = "{{ asset('public/uploads') }}/" + res.w9;
                            var fileNameParts = res.w9.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                w9 = '<a href="' + link_w9 + '" download><img src="' + link_w9 +
                                    '" class="img w-100"/></a>';
                            } else {
                                w9 = '<a href="' + link_w9 +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var saftey_report = '';
                        if (res.saftey_report != null) {
                            var link_saftey_report = "{{ asset('public/uploads') }}/" + res
                                .saftey_report;
                            var fileNameParts = res.saftey_report.split('.');
                            var fileExtension = fileNameParts[fileNameParts.length - 1];
                            if (fileExtension != 'pdf') {
                                saftey_report = '<a href="' + link_saftey_report +
                                    '" download><img src="' + link_saftey_report +
                                    '" class="img w-100"/></a>';
                            } else {
                                saftey_report = '<a href="' + link_saftey_report +
                                    '"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                            }
                        }
                        var four_pics = JSON.parse(res.four_pics);
                        var imgs = "";
                        if (four_pics != null && four_pics.length > 0) {
                            four_pics.forEach(p => {
                                var img = "{{ asset('public/uploads') }}/" + p;
                                imgs += `<div class="col-md-6"><a href="` + img +
                                    `" download="download"><img src="` + img +
                                    `" class="w-100"/></a></div>`;
                            });
                        }
                        if (res.driver_name != null) {
                            driver_name = res.driver_name;
                        } else {
                            driver_name = 'Not allocated any driver';
                        }

                        function formatDate(inputDate) {
                            var parsedDate = new Date(inputDate);
                            var formattedDate = (parsedDate.getMonth() + 1).toString().padStart(2,
                                '0') + '-' +
                                parsedDate.getDate().toString().padStart(2, '0') + '-' +
                                parsedDate.getFullYear();
                            return formattedDate;
                        }
                        $('#ownerdetailModal').modal('toggle');
                        $('#truckdetailModal').modal('toggle');
                        $('#truck_details').html(
                            `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Truck #</th>
                                        <th>VIN #</th>
                                        <th>Year</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>` + res.t_num + `</td>
                                        <td>` + res.vin + `</td>
                                        <td>` + res.year + `</td>
                                        <td>` + res.make + `</td>
                                        <td>` + res.model + `</td>
                                        <td>` + res.created_on + `</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5 class="mb-1">Owner Name</h5><p>` + res.owner_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Driver Name</h5><p>` + driver_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Plate Number</h5><p>` + res.plate_number + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">2290 Renewal Date</h5><p>` + formatDate(res
                                .renewal_date_2290) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Truck Address</h5><p>` + res.truck_address + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Trailer</h5><p>` + res.trailer + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Card Renewal Date</h5><p>` + formatDate(res
                                .card_renew_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Sticker Renewal Date</h5><p>` + formatDate(res
                                .sticker_renew_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Insurance Name</h5><p>` + res
                            .damage_insurance_name + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Insurance Policy Number</h5><p>` + res
                            .insurance_policy_number + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Effective Date</h5><p>` + formatDate(res
                                .damage_effective_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Damage Expiry Date</h5><p>` + formatDate(res
                                .damage_expiry_date) + `</p></div>
                            <div class="col-md-6"><h5 class="mb-1">Trailer Registration Renewal Date</h5><p>` +
                            formatDate(res.trailer_reg_renew_date) + `</p></div>
                        </div>`


                        );
                    }
                }
            });
        });
  </script>
@endsection
