@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Owners</h4>
    @php
    if(isset($_GET)){
    $sortBy = request('field');
    $sortOrder = request('orderBy');
    $start = request('start_date');
    $end = request('end_date');
    $search = @$_GET['search'];
    $perPage = 10;

    // Create the base query builder
    $owners = DB::table('owners')->where('is_deleted', 0);

    if($search){
        $owners->where(function($query) use($search){
            $query->orWhere('company_name', 'like', '%'.@$search.'%');
            $query->orWhere('owner_name', 'like', '%'.@$search.'%');
            $query->orWhere('address', 'like', '%'.@$search.'%');
            $query->orWhere('phone', 'like', '%'.@$search.'%');
            $query->orWhere('email', 'like', '%'.@$search.'%');
            $query->orWhere('routing', 'like', '%'.@$search.'%');
            $query->orWhere('account', 'like', '%'.@$search.'%');
            $query->orWhere('email', 'like', '%'.@$search.'%');
        });
    }

    if($start || $end){
        $owners->where(function($qry) use($start, $end){
            if($start){
                $qry->where('created_on', '>=', @$start.' 00:00:01');
            }
            if($end){
                $qry->where('created_on', '<=', @$end.' 23:59:00');
            }
        });
    }

    if(isset($sortBy)){
        $owners->orderBy($sortBy, $sortOrder);
    }
    // Retrieve the results with pagination
    $owners = $owners->get();
} else {
    // If there's no search, simply paginate all results
    $owners = DB::table('owners')->where('is_deleted', 0)
        ->orderBy('id', 'asc')
        ->get();
}

    @endphp
    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">
        <div class="row">
            <div class="col-md-6">
                Owners List
            </div>
            <div class="col-md-6 text-right float-right">
                <a href="{{url('Owners/Add')}}" class="btn btn-outline-primary btn-sm" style="float: right;"><i class="fa fa-plus"></i> Add Owner</a>
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
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=company_name">Company</a></th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=owner_name">Owner Name</a></th>
              <th>Address</th>
              <th>Phone</th>
              <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=email">Email</a></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @php
                // $rowCounter = ($owners->currentPage() - 1) * $owners->perPage() + 1;
            @endphp
            @foreach ($owners as $o)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $o->company_name }}</td>
                    <td>{{ $o->owner_name }}</td>
                    <td>{{ $o->address }}</td>
                    <td>{{ $o->phone }}</td>
                    <td>{{ $o->email }}</td>
                    <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{url('Owner/Edit')}}/{{$o->id}}"
                              ><i class="ti ti-pencil me-1"></i> Edit</a
                            >
                            <a class="dropdown-item" href="{{url('Delete-Owner')}}/{{$o->id}}"
                              ><i class="ti ti-trash me-1"></i> Delete</a
                            >
                            <a class="dropdown-item owner-detail" href="javascript:void(0);" data-id="{{$o->id}}"
                              ><i class="ti ti-info-circle me-1"></i> Details</a
                            >
                            <a class="dropdown-item" href="{{url('archive-owner')}}/{{$o->id}}"
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
      {{-- {{ $owners->appends(request()->input())->links() }} --}}
      {{-- {{ $owners->appends(request()->input())->links('pagination::bootstrap-5') }} --}}
    </div>
    <!--/ Basic Bootstrap Table -->


  </div>


  <!-- Modal -->
<div class="modal fade" id="ownerdetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Owner Detail</h5>
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
    $('tbody').on('click', '.owner-detail', function(){
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'ajax',
            method: 'POST',
            data: {'id': id},
            url: "{{url('getOwnerDetails')}}",
            success: function(res){
                if(res){
                    var path = "{{asset('public/uploads')}}/"+res.license;
                    $('#ownerdetailModal').modal('toggle');
                    $('#details').html(
                        `<div class="table-reponsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Owner Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Time Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>`+res.company_name+`</td>
                                        <td>`+res.owner_name+`</td>
                                        <td>`+res.phone+`</td>
                                        <td>`+res.email+`</td>
                                        <td>`+res.created_on+`</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                          <div class="col-md-4"><h5>Routing #</h5><p>`+res.routing+`</p></div>
                            <div class="col-md-4"><h5>Account</h5><p>`+res.account+`</p></div>
                            <div class="col-md-4"><h5>SSN</h5><p>`+res.ssn+`</p></div>
                            <div class="col-md-12"><h5>License:</h5><a href="`+path+`" download="download"><img src="`+path+`" class="img w-100"/></a></div>
                        </div>
                        `


                    );
                }
            }
        });
    });
  </script>
@endsection
