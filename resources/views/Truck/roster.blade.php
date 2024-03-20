@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Truck /</span> Truck Roster</h4>

    <!-- Buttons for Filtering Trucks -->
    <div class="mb-4">
        <button class="btn btn-primary" id="activeBtn">Active</button>
        <button class="btn btn-secondary" id="inactiveBtn">Inactive</button>
        {{-- <button class="btn btn-info" id="underDispatchBtn">Under Dispatch</button>
        <button class="btn btn-warning" id="notUnderDispatchBtn">Not Under Dispatch</button> --}}
    </div>

    <!-- Truck Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Truck Roster</h5>
        </div>
        <div class="card-body">
            <table class="table" id="truckTable">
                <thead>
                    <tr>
                        <th>Truck Number</th>
                        <th>License Plate Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table rows will be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="addNewCCModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add description here</h3>
          </div>
          <form method="POST" id="addNewCCForm" class="row g-3" action="">
            @csrf
            <div class="col-12">
              <label for="">Description</label>
              <textarea required class="form-control" name="description" id="" rows="4"></textarea>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Dispatch</button>
              <button
                type="reset"
                class="btn btn-label-secondary btn-reset"
                data-bs-dismiss="modal"
                aria-label="Close"
              >
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
    // Function to load trucks based on the selected filter
    function loadTrucks(filter) {
        $.ajax({
            url: "{{ url('loadTrucks') }}",
            method: "POST",
            data: { filter: filter },
            success: function(response) {
                // Populate the table with data from the response
                $('#truckTable tbody').html(response);
            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error(error);
            }
        });
    }

    $(document).ready(function() {
        // Initial load of the 'Active' trucks
        loadTrucks('active');

        // Filter buttons click event handlers
        $('#activeBtn').click(function() {
            loadTrucks('active');
        });

        $('#inactiveBtn').click(function() {
            loadTrucks('inactive');
        });

        // $('#underDispatchBtn').click(function() {
        //     loadTrucks('under_dispatch');
        // });

        // $('#notUnderDispatchBtn').click(function() {
        //     loadTrucks('not_under_dispatch');
        // });

        $('tbody').on('click', '.btn-change-status', function(){
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            $.ajax({
                type:'ajax',
                method: 'POST',
                data: {'id': id, 'status': status},
                url: '{{url("Change-Status")}}',
                success: function(res){
                    loadTrucks('active');
                }
            });
        });
    });

    $('body').on('click','.btn-dispatch',function(){
       var id=$(this).attr('data-id');
       $('#addNewCCModal').modal('show')
       $('#addNewCCForm').attr('action','{{url("dispatch/truck/")}}/'+id)
    })

    $('body').on('click','.btn-return',function(){
        var id=$(this).attr('data-id');
        window.location="{{url('Return/truck/')}}/"+id;
    })
</script>
@endsection
