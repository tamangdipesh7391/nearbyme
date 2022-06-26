@extends('user.main')
@section('content')
<h4 class="mt-2"><i class="fa fa-users mr-1 "></i> Your request history</h4>
@if (Session::has('success'))
<div class="alert alert-success">
    {{ Session::get('success') }}
</div>
@endif
@if (Session::has('error'))
<div class="alert alert-danger">
    {{ Session::get('error') }}
</div>  
@endif
<hr>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
      <a class="nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-all" role="tab" aria-controls="nav-profile" aria-selected="false">All</a>
      <a class="nav-link " id="nav-home-tab" data-toggle="tab" href="#nav-pending" role="tab" aria-controls="nav-home" aria-selected="true">Pending</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-confirmed" role="tab" aria-controls="nav-contact" aria-selected="false">Confirmed</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-rejected" role="tab" aria-controls="nav-contact" aria-selected="false">Rejected</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-canceled" role="tab" aria-controls="nav-contact" aria-selected="false">Canceled</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-trashed" role="tab" aria-controls="nav-contact" aria-selected="false">Trashed</a>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-profile-tab">
      @if (count($requested_services) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Requested to</th>
          <th>Profession</th>
          <th>Status</th>
          <th>Requested Date</th>
          <th>Actions</th>
        </tr>
        @foreach ($requested_services as $key => $request)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $request->provider->name }}</td>
          <td>{{$request->provider->profession->name}}</td>
        
          <td>
            @if($request->status == 'pending')
            <span class="badge badge-warning">Pending</span>
            @elseif($request->status == 'confirmed')
            <span class="badge badge-success">Confirmed</span>
            @elseif($request->status == 'rejected')
            <span class="badge badge-danger">Rejected</span>
            @endif

          </td>
            <td>{{ $request->created_at }}</td>
          <td>
            <a href="{{route('user.request.manage',$request->id)}}" class="btn btn-warning btn-sm">Cancel</a>
            <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.soft_delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no request yet.
      </p>
      @endif
    </div>
    <div class="tab-pane fade  " id="nav-pending" role="tabpanel" aria-labelledby="nav-home-tab">
        @if (count($pending_services) > 0)
        <table class="table table-bordered table-hover">
            <tr>
            <th>SN</th>
            <th>Requested to</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Actions</th>
            </tr>
            @foreach ($pending_services as $key => $request)
            <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $request->provider->name }}</td>
            <td>{{$request->provider->profession->name}}</td>
            
            <td>
                @if($request->status == 'pending')
                <span class="badge badge-warning">Pending</span>
                @elseif($request->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
                @elseif($request->status == 'rejected')
                <span class="badge badge-danger">Rejected</span>
                @endif
    
            </td>
                <td>{{ $request->created_at }}</td>
            <td>
                <a href="{{route('user.request.manage',$request->id)}}" class="btn btn-warning btn-sm">Cancel</a>
                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.soft_delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
            </td>
            </tr>
            @endforeach
        </table>
        @else
        <p class="mt-3" style="color: red">
            There are no request yet.
        </p>
        @endif
    </div>
   
  
    <div class="tab-pane fade" id="nav-confirmed" role="tabpanel" aria-labelledby="nav-contact-tab">
        @if (count($confirmed_services) > 0)
        <table class="table table-bordered table-hover">
            <tr>
            <th>SN</th>
            <th>Requested to</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Actions</th>
            </tr>
            @foreach ($confirmed_services as $key => $request)
            <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $request->provider->name }}</td>
            <td>{{$request->provider->profession->name}}</td>
            
            <td>
                @if($request->status == 'pending')
                <span class="badge badge-warning">Pending</span>
                @elseif($request->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
                @elseif($request->status == 'rejected')
                <span class="badge badge-danger">Rejected</span>
                @endif
    
            </td>
                <td>{{ $request->created_at }}</td>
            <td>
                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.soft_delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
            </td>
            </tr>
            @endforeach
        </table>
        @else
        <p class="mt-3" style="color: red">
            There are no request yet.
        </p>
        @endif
    </div>

    <div class="tab-pane fade" id="nav-rejected" role="tabpanel" aria-labelledby="nav-contact-tab">
        @if (count($rejected_services) > 0)
        <table class="table table-bordered table-hover">
            <tr>
            <th>SN</th>
            <th>Requested to</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Actions</th>
            </tr>
            @foreach ($rejected_services as $key => $request)
            <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $request->provider->name }}</td>
            <td>{{$request->provider->profession->name}}</td>
            
            <td>
                @if($request->status == 'pending')
                <span class="badge badge-warning">Pending</span>
                @elseif($request->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
                @elseif($request->status == 'rejected')
                <span class="badge badge-danger">Rejected</span>
                @endif
    
            </td>
                <td>{{ $request->created_at }}</td>
            <td>
                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.soft_delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
            </td>
            </tr>
            @endforeach
        </table>
        @else
        <p class="mt-3" style="color: red">
            There are no request yet.
        </p>
        @endif
      </div>
  
      <div class="tab-pane fade" id="nav-canceled" role="tabpanel" aria-labelledby="nav-contact-tab">
        @if (count($canceled_services) > 0)
        <table class="table table-bordered table-hover">
            <tr>
            <th>SN</th>
            <th>Requested to</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Actions</th>
            </tr>
            @foreach ($canceled_services as $key => $request)
            <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $request->provider->name }}</td>
            <td>{{$request->provider->profession->name}}</td>
            
            <td>
                @if($request->status == 'pending')
                <span class="badge badge-warning">Pending</span>
                @elseif($request->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
                @elseif($request->status == 'rejected')
                <span class="badge badge-danger">Rejected</span>
                @endif
    
            </td>
                <td>{{ $request->created_at }}</td>
            <td>
                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.soft_delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
            </td>
            </tr>
            @endforeach
        </table>
        @else
        <p class="mt-3" style="color: red">
            There are no request yet.
        </p>
        @endif
      </div>
    <div class="tab-pane fade" id="nav-trashed" role="tabpanel" aria-labelledby="nav-contact-tab">
        @if (count($trashed_services) > 0)
        <table class="table table-bordered table-hover">
            <tr>
            <th>SN</th>
            <th>Requested to</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Actions</th>
            </tr>
            @foreach ($trashed_services as $key => $request)
            <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $request->provider->name }}</td>
            <td>{{$request->provider->profession->name}}</td>
            
            <td>
                @if($request->status == 'pending')
                <span class="badge badge-warning">Pending</span>
                @elseif($request->status == 'confirmed')
                <span class="badge badge-success">Confirmed</span>
                @elseif($request->status == 'rejected')
                <span class="badge badge-danger">Rejected</span>
                @endif
    
            </td>
                <td>{{ $request->created_at }}</td>
            <td>
                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.restore', $request->id) }}" class="btn btn-danger btn-sm">Restore</a>

                <a onclick="return confirm('Are you sure?')" href="{{ route('user.request.delete', $request->id) }}" class="btn btn-danger btn-sm">Delete</a>
            </td>
            </tr>
            @endforeach
        </table>
        @else
        <p class="mt-3" style="color: red">
            There are no request yet.
        </p>
        @endif
    </div>
  </div>

  
@endsection