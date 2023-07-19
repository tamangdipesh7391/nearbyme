@extends('admin.main')
@section('content')
<h4 class="mt-2"><i class="fa fa-users mr-1 "></i> List of Users</h4>
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
      @if (count($new_users) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($new_users as $key => $user)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            @if($user->status == 'new')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class=" btn btn-sm btn-warning">{{ $user->status }}</button>
            @elseif($user->status == 'active')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class="btn btn-sm btn-success">{{ $user->status }}</button>
            @elseif($user->status == 'suspended')
           <button data-toggle="modal" data-target="#newModal{{$key}}" class="btn btn-sm btn-danger">{{ $user->status }}</button>
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
                  <form action="{{route('admin.users.manage',$user->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($user->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($user->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($user->status == 'suspended') selected @endif>Suspended</option>
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
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.users.soft_delete', $user->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no new users.
      </p>
      @endif
    </div>
    <div class="tab-pane fade  " id="nav-active" role="tabpanel" aria-labelledby="nav-home-tab">
      
      @if (count($active_users) > 0)
     
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($active_users as $key => $user)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            @if($user->status == 'new')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class=" btn btn-sm btn-warning">{{ $user->status }}</button>
            @elseif($user->status == 'active')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-success">{{ $user->status }}</button>
            @elseif($user->status == 'suspended')
           <button data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-danger">{{ $user->status }}</button>
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
                  <form action="{{route('admin.users.manage',$user->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($user->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($user->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($user->status == 'suspended') selected @endif>Suspended</option>
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
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.users.soft_delete', $user->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no active users.
      </p>
        
      @endif
    </div>
   
  
    <div class="tab-pane fade" id="nav-suspended" role="tabpanel" aria-labelledby="nav-contact-tab">
      @if (count($suspended_users) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($suspended_users as $key => $user)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            @if($user->status == 'new')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class=" btn btn-sm btn-warning">{{ $user->status }}</button>
            @elseif($user->status == 'active')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class="btn btn-sm btn-success">{{ $user->status }}</button>
            @elseif($user->status == 'suspended')
           <button data-toggle="modal" data-target="#suspendedModal{{$key}}" class="btn btn-sm btn-danger">{{ $user->status }}</button>
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
                  <form action="{{route('admin.users.manage',$user->id)}}" method="POST">
                  <div class="modal-body">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                       
                        <select name="status" id="status" class="form-control">
                          <option value="new" @if ($user->status == 'new') selected @endif>New</option>
                          <option value="active" @if ($user->status == 'active') selected @endif>Active</option>
                          <option value="suspended" @if ($user->status == 'suspended') selected @endif>Suspended</option>
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
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.users.soft_delete', $user->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no suspended users.
      </p>
      @endif
    </div>

    <div class="tab-pane fade" id="nav-trashed" role="tabpanel" aria-labelledby="nav-contact-tab">
      @if (count($trashed_users) > 0)
      <table class="table table-bordered table-hover">
        <tr>
          <th>SN</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        @foreach ($trashed_users as $key => $user)
        <tr>
          <td>{{ ++$key }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            @if($user->status == 'new')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class=" btn btn-sm btn-warning">{{ $user->status }}</button>
            @elseif($user->status == 'active')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-success">{{ $user->status }}</button>
            @elseif($user->status == 'suspended')
           <button disabled data-toggle="modal" data-target="#activeModal{{$key}}" class="btn btn-sm btn-danger">{{ $user->status }}</button>
            @endif

          </td>
          <td>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.users.restore', $user->id) }}" class="btn btn-success btn-sm">Restore</a>
            <a onclick="return confirm('Are you sure?')" href="{{ route('admin.users.delete', $user->id) }}" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else
      <p class="mt-3" style="color: red">
        There are no trashed users.
      </p>
      @endif
    </div>
  </div>

  
@endsection