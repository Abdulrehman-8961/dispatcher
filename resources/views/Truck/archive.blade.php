@extends('components/header')
@section('main')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Trucks /</span> Trucks</h4>
    @php
        $data=DB::table('archived')->where('truck_id',$id)->get();


    @endphp

    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">
        <div class="row">
            <div class="col-md-6">
                Archive List
            </div>
            <div class="col-md-6">
            </div>
        </div>

      </h5>

      <div class="row">
        <div class="col-md-12">

          <div class="table-responsive ">
            <table class="table mb-5">
              <thead>
                <tr>
                  <th>Document</th>
                  <th>Name</th>
                  <th>Date/Time</th>
                  <th>Download</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @foreach ($data as $d)
                @php
                $file_path = $d->document_name;
                $file_info = pathinfo($file_path);
                $extension = $file_info['extension'];
                @endphp


                    <tr>
                      <td>@if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png')
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar">
                            <img src="{{asset('public/uploads')}}/{{$d->document_name}}" class=" rounded-circle" alt="">
                          </div>
                        </div>
                        @else
                        <i class="ti ti-file-invoice text-success fs-2 ms-1"></i>
                        @endif</td>
                        <td>{{$d->name}}</td>
                        <td>{{date('M-d-Y H:i:s', strtotime($d->created_on))}}</td>
                        <td>
                          <a href="{{asset('public/uploads')}}/{{$d->document_name}}" download="" target="_blank"><i class="ti ti-download text-primary fs-4"></i></a>
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@endsection
