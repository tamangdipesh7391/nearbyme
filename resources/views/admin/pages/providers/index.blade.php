@extends('admin.main')
@section('content')
<h4 class="mt-2"><i class="fa fa-users mr-1 "></i> List of Providers</h4>
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
      <a class="nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-new" role="tab" aria-controls="nav-profile" aria-selected="false">New</a>
      <a class="nav-link " id="nav-home-tab" data-toggle="tab" href="#nav-active" role="tab" aria-controls="nav-home" aria-selected="true">Active</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-suspended" role="tab" aria-controls="nav-contact" aria-selected="false">Suspended</a>
      <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-trashed" role="tab" aria-controls="nav-contact" aria-selected="false">Trashed</a>
    </div>
  </nav>
  <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-new" role="tabpanel" aria-labelledby="nav-profile-tab">
      @if (count($new_providers) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($new_providers as $key => $provider)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $provider->name }}</td>
          <td>{{ $provider->email }}</td>
          <td>
            @if($provider->status == 'new')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class=" btn btn-sm btn-warning">{{ $provider->status }}</button>
            @elseif($provider->status == 'active')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class="btn btn-sm btn-success">{{ $provider->status }}</button>
            @elseif($provider->status == 'suspended')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class="btn btn-sm btn-danger">{{ $provider->status }}</button>
            @endif

            {{-- modal start --}}
            <div class="modal fade" id="newModal{{$key}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Choose status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{route('admin.providers.manage',$provider->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($provider->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($provider->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($provider->status == 'suspended') selected @endif>Suspended</option>
                        </select>
                   
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Change Status</button>
                  </div>
                 </form>
                </div>
              </div>
            </div>
            
            {{-- modal end --}}
          </td>
          <td>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.providers.soft_delete', $provider->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no new providers.
      </p>
      @endif
    </div>
    <div class="tab-pane fade  " id="nav-active" role="tabpanel" aria-labelledby="nav-home-tab">
      
      @if (count($active_providers) > 0)
     
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($active_providers as $key => $provider)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $provider->name }}</td>
          <td>{{ $provider->email }}</td>
          <td>
            @if($provider->status == 'new')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class=" btn btn-sm btn-warning">{{ $provider->status }}</button>
            @elseif($provider->status == 'active')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-success">{{ $provider->status }}</button>
            @elseif($provider->status == 'suspended')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-danger">{{ $provider->status }}</button>
            @endif

            {{-- modal start --}}
            <div class="modal fade" id="activeModal{{$key}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Choose status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{route('admin.providers.manage',$provider->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($provider->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($provider->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($provider->status == 'suspended') selected @endif>Suspended</option>
                        </select>
                   
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Change Status</button>
                  </div>
                 </form>
                </div>
              </div>
            </div>
            
            {{-- modal end --}}
          </td>
          <td>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.providers.soft_delete', $provider->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no active providers.
      </p>
        
      @endif
    </div>
   
  
    <div class="tab-pane fade" id="nav-suspended" role="tabpanel" aria-labelledby="nav-contact-tab">
      @if (count($suspended_providers) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($suspended_providers as $key => $provider)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $provider->name }}</td>
          <td>{{ $provider->email }}</td>
          <td>
            @if($provider->status == 'new')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class=" btn btn-sm btn-warning">{{ $provider->status }}</button>
            @elseif($provider->status == 'active')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class="btn btn-sm btn-success">{{ $provider->status }}</button>
            @elseif($provider->status == 'suspended')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class="btn btn-sm btn-danger">{{ $provider->status }}</button>
            @endif

            {{-- modal start --}}
            <div class="modal fade" id="suspendedModal{{$key}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Choose status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form action="{{route('admin.providers.manage',$provider->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($provider->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($provider->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($provider->status == 'suspended') selected @endif>Suspended</option>
                        </select>
                   
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Change Status</button>
                  </div>
                 </form>
                </div>
              </div>
            </div>
            
            {{-- modal end --}}
          </td>
          <td>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.providers.soft_delete', $provider->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no suspended providers.
      </p>
      @endif
    </div>

    <div class="tab-pane fade" id="nav-trashed" role="tabpanel" aria-labelledby="nav-contact-tab">
      @if (count($trashed_providers) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($trashed_providers as $key => $provider)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $provider->name }}</td>
          <td>{{ $provider->email }}</td>
          <td>
            @if($provider->status == 'new')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class=" btn btn-sm btn-warning">{{ $provider->status }}</button>
            @elseif($provider->status == 'active')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-success">{{ $provider->status }}</button>
            @elseif($provider->status == 'suspended')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-danger">{{ $provider->status }}</button>
            @endif

          </td>
          <td>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.providers.restore', $provider->id) }}" class="btn btn-success btn-sm">Restore</a>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.providers.delete', $provider->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no trashed providers.
      </p>
      @endif
    </div>
  </div>

  
@endsection