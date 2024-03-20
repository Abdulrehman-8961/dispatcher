@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Percentage</h4>
    @php
    $result = DB::table('settings')->get();

    @endphp
    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">
        <div class="row">
            <div class="col-md-6">
Percentage Settings
            </div>
        </div>
      </h5>
      <?php  $settings=DB::Table('settings')->first();?>
      <form method="post" action="{{url('update-settings')}}">@csrf
      <div class="row ps-3">
        <div class="col-md-3">
          <input name="value_1" class="form-control" value="{{$settings->value_1}}"  required>

        </div>

        <div class="col-md-3">
            <input name="value_2" class="form-control"  value="{{$settings->value_2}}" required>

          </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary">Update</button>
        </div>
      </div>
      <hr>
      </form>




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
    $('body').on('click','.escrow_return',function(){
      var truckId=$(this).data("id");
      window.location="{{url('go/escrow/return')}}/"+truckId;
    })

  </script>
@endsection
