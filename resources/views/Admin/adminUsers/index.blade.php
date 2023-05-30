<x-layouts.admin>
  @section('title', 'Admin Users')
 <!-- Content Header (Page header) -->
<x-slot name="breadcrumb">
    <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Admin User Manager</h1>
        </div>
        <div class="col-sm-6">
          {{-- <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Simple Tables</li>
          </ol> --}}
          {{ Breadcrumbs::render('common',['append' => [['label'=> $getController, 'route'=> \Request::route()->getName()]]]) }}
        </div> 
      </div>
</x-slot>

<x-slot name="content">
  <!-- Main content -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Admin Users</h3>

          <div class="card-tools">
                <a href="{{ route("admin.admin-users.create") }}" class="btn btn-block btn-primary btn-sm" title="Add Admin User"><i class="fa fa-plus"></i> Add Admin User</a>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0"> 
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width: 7%">#</th>
                <th>Privilege</th>
                <th>@sortablelink('first_name', 'Name')</th>
                <th>@sortablelink('email')</th>
                <th>@sortablelink('status', 'Status')</th>
                <th>@sortablelink('email_verified_at', 'Verified')</th>
                <th>@sortablelink('created_at', 'Created')</th>
                <th scope="col" class="actions" width="12%">Action</th>
              </tr>
            </thead>
            @if($adminUsers->count() > 0)
            <tbody>
                @php
                $i = (($adminUsers->currentPage() - 1) * ($adminUsers->perPage()) + 1)
                @endphp
                @foreach($adminUsers as $adminUser)
                  <tr class="row-{{ $adminUser->id }}">
                      <td> {{ $i }}. </td>
                      <td>
                        {{ $adminUser->role->title ?? "" }}
                      </td>
                      <td>{{ $adminUser->name }}</td>
                      <td>{{ $adminUser->email }}</td>
                      <td>
                          @if ($adminUser->status == 1)
                              <span class='btn1 btn-block btn-success btn-xs updateStatus text-center' data-value="1" data-column="status">Active</span>
                          @else
                              <span class='btn1 btn-block btn-danger btn-xs updateStatus text-center' data-value="0">In-Active</span>
                          @endif
                      </td>
                      <td>
                          @if ($adminUser->hasVerifiedEmail())
                              <span class='btn1 btn-block btn-success btn-xs text-center' data-url="">Verified</span>
                          @else
                              <span class='btn1 btn-block btn-danger btn-xs text-center'>Not Verified</span>
                          @endif
                      </td>
                      <td>{{ $adminUser->created_at->format('Y-m-d') }}</td>
                      <td class="actions">
                          @php
                              $queryStr['id'] = $adminUser->id;
                              $queryStr = array_merge( $queryStr , app('request')->query());
                          @endphp
                          <div class="form-group">
                              <a href="{{ route('admin.admin-users.show', $adminUser->id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View User Detail" title="View Admin User Detail" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                              <a href="{{ route('admin.admin-users.edit', $adminUser->id) }}" class="btn btn-success btn-xs" data-toggle="tooltip" alt="Edit" title="Edit Admin User" data-original-title="Edit User"><i class="fa fa-edit"></i></a>
                              @if(Auth::user()->id != $adminUser->id)                              
                              <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $adminUser->name }}" title="Delete Admin User" data-url="{{ route('admin.admin-users.destroy', $adminUser->id) }}"  data-action="delete" data-message="Are you sure want to delete this user?"><i class="fa fa-trash"></i></a>
                              @endif
                          </div>
                      </td>
                  </tr>
                @php
                    $i++;
                @endphp
                @endforeach
            </tbody>
            @else
            <tfoot>
                <tr>
                    <td colspan='10' align='center'> <strong>No records available</strong> </td>
                </tr>
            </tfoot>
            @endif
          </table>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          {{-- <ul class="pagination pagination-sm m-0 float-right">
            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
          </ul> --}}
          {!! $adminUsers->appends(\Request::except('page'))->render() !!}
        </div>
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.content -->
</x-slot>
</x-layouts.admin>
