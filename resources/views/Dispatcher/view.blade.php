@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dispatcher /</span> Dispatcher</h4>
    @php
    if(isset($_GET)){
    $sortBy = request('field');
    $sortOrder = request('orderBy');
    $start = request('start_date');
    $end = request('end_date');
    $search = @$_GET['search'];
    $perPage = 10;

    // Create the base query builder
    $dispatcher = DB::table('dispatchers')->where('is_deleted', 0);

    if($search){
        $dispatcher->where(function($query) use($search){
            $query->orWhere('dispatcher_name', 'like', '%'.@$search.'%');
            $query->orWhere('phone', 'like', '%'.@$search.'%');
            $query->orWhere('email', 'like', '%'.@$search.'%');
            $query->orWhere('salary', 'like', '%'.@$search.'%');
            $query->orWhere('address', 'like', '%'.@$search.'%');
            $query->orWhere('routing_number', 'like', '%'.@$search.'%');
            $query->orWhere('account_number', 'like', '%'.@$search.'%');
            $query->orWhere('driver_license_number', 'like', '%'.@$search.'%');
            $query->orWhere('ssn_number', 'like', '%'.@$search.'%');
        });
    }

    if($start || $end){
        $dispatcher->where(function($qry) use($start, $end){
            if($start){
                $qry->where('created_on', '>=', @$start.' 00:00:01');
            }
            if($end){
                $qry->where('created_on', '<=', @$end.' 23:59:00');
            }
        });
    }

    if(isset($sortBy)){
        $truck->orderBy($sortBy, $sortOrder);
    }
    // Retrieve the results with pagination
    $dispatcher = $dispatcher->get();
} else {
    // If there's no search, simply paginate all results
    $dispatcher = DB::table('dispatchers')->where('is_deleted', 0)
        ->orderBy('id', 'asc')
        ->get();
}

    @endphp
    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">
        <div class="row">
            <div class="col-md-6">
                Dispatcher List
            </div>
            <div class="col-md-6 text-right float-right">
                <a href="{{url('Dispatcher/Add')}}" class="btn btn-outline-primary btn-sm" style="float: right;"><i class="fa fa-plus"></i> Add Dispatcher</a>
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
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=dispatcher_name">Dispatcher Name</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=phone">Phone</a></th>
              <th>Address</th>
              <th>Salary</th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=email">Email</a></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach ($dispatcher as $o)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $o->dispatcher_name }}</td>
                    <td>{{ $o->phone }}</td>
                    <td>{{ $o->address }}</td>
                    <td>{{ $o->salary }}</td>
                    <td>{{ $o->email }}</td>
                    <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{url('Edit-Dispatcher')}}/{{$o->id}}"
                              ><i class="ti ti-pencil me-1"></i> Edit</a
                            >
                            <a class="dropdown-item" href="{{url('Delete-Dispatcher')}}/{{$o->id}}"
                              ><i class="ti ti-trash me-1"></i> Delete</a
                            >
                            <a class="dropdown-item dispatcher-detail" href="javascript:void(0);" data-id="{{$o->id}}"
                              ><i class="ti ti-info-circle me-1"></i> Details</a
                            >
                            <a class="dropdown-item" href="{{url('archive-dispatcher')}}/{{$o->id}}"
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
    </div>
    <!--/ Basic Bootstrap Table -->


  </div>


  <!-- Modal -->
<div class="modal fade" id="ownerdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Dispatcher Detail</h5>
          <button type="button" class="close btn btn-close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="details">
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>

    $('.btn-close').on('click', function(){
        $('#ownerdetailModal').modal('toggle');
    })
    $('tbody').on('click', '.dispatcher-detail', function(){
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'ajax',
            method: 'POST',
            data: {'id': id},
            url: "{{url('getDispatcherDetails')}}",
            success: function(res){
                if(res){
                    // var driver_licenses_path = "{{asset('public/uploads')}}/"+res.driver_licenses_path;
                    var driver_licenses_path='';
                  if(res.driver_licenses_path != null){
                    var link_driver_licenses_path = "{{asset('public/uploads')}}/"+res.driver_licenses_path;
                    var fileNameParts = res.driver_licenses_path.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      driver_licenses_path='<a href="'+link_driver_licenses_path+'" download><img src="'+link_driver_licenses_path+'" class="img w-100"/></a>';
                    }else{
                      driver_licenses_path= '<a href="'+link_driver_licenses_path+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    // var ssn_pic_path = "{{asset('public/uploads')}}/"+res.ssn_pic_path;
                    var ssn_pic_path='';
                  if(res.ssn_pic_path != null){
                    var link_ssn_pic_path = "{{asset('public/uploads')}}/"+res.ssn_pic_path;
                    var fileNameParts = res.ssn_pic_path.split('.');
                    var fileExtension = fileNameParts[fileNameParts.length - 1];
                    if(fileExtension != 'pdf'){
                      ssn_pic_path='<a href="'+link_ssn_pic_path+'" download><img src="'+link_ssn_pic_path+'" class="img w-100"/></a>';
                    }else{
                      ssn_pic_path= '<a href="'+link_ssn_pic_path+'"><span class="badge bg-label-primary rounded-pill ms-auto">Download Pdf</span></a>';
                    }
                    }
                    $('#ownerdetailModal').modal('toggle');
                    $('#details').html(
                        `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Dispatcher Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Time Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>`+res.dispatcher_name+`</td>
                                        <td>`+res.phone+`</td>
                                        <td>`+res.email+`</td>
                                        <td>`+res.address+`</td>
                                        <td>`+res.created_on+`</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><h5>Salary</h5><p>`+res.salary+`</p></div>
                            <div class="col-md-6"><h5>Routing Number</h5><p>`+res.routing_number+`</p></div>
                            <div class="col-md-6"><h5>Account Number</h5><p>`+res.account_number+`</p></div>
                            <div class="col-md-6"><h5>Driver License Number</h5><p>`+res.driver_license_number+`</p></div>
                            <div class="col-md-12"><h5>Driver License:</h5>`+driver_licenses_path+`</div>
                            <div class="col-md-12"><h5>SSN:</h5>`+ssn_pic_path+`</div>
                        </div>
                        `


                    );
                }
            }
        });
    });
  </script>
@endsection
