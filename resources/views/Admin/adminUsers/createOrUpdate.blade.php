<x-layouts.master>
  @section('title', !empty($adminUser) ? 'Edit Admin User' : 'Add Admin User')
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
          @php
           // dd(\Request::route()->getName());
          @endphp
          {{ Breadcrumbs::render('common',['append' => [['label'=> $getController, 'route'=> 'admin.admin-users.index'], ['label' => !empty($adminUser) ? 'Edit Admin User' : 'Add Admin User' ]]]) }}
        </div>
      </div>
</x-slot>
<x-slot name="content">
  <!-- Main content -->
@if(isset($adminUser) && $adminUser->exists)
  @php
      $queryStr['admin_user'] = $adminUser->id;
      $queryStr = array_merge( $queryStr , app('request')->query());
  @endphp
      {{ Form::model($adminUser, ['url' => route('admin.admin-users.update', $queryStr), 'method' => 'patch','files' => true]) }}
  @else
      {{ Form::open(['url' => route('admin.admin-users.store', app('request')->query()),'files' => true]) }}
  @endif
  <div class="row">
    <div class="col-md-12">
      <!-- SELECT2 EXAMPLE -->
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">{{ !empty($adminUser) ? 'Edit Admin User' : 'Add Admin User'  }}</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row required">
                {{ Form::label('first_name', __('First Name'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{ Form::text('first_name', old('first_name'), ['class' => 'form-control' . ($errors->has('first_name') ? ' is-invalid' : ''), 'placeholder' => 'First Name']) }}
                  @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                {{ Form::label('last_name', __('Last Name'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{ Form::text('last_name', old('last_name'), ['class' => 'form-control' . ($errors->has('last_name') ? ' is-invalid' : ''), 'placeholder' => 'Last Name']) }}
                  @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row">
                {{ Form::label('mobile', __('Mobile Number'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{ Form::text('mobile', old('mobile'), ['class' => 'form-control' . ($errors->has('mobile') ? ' is-invalid' : ''), 'placeholder' => 'Mobile Number']) }}
                  @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row required">
                {{ Form::label('email', __('Email'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{ Form::text('email', old('email'), ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email ID']) }}
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              @if(!isset($adminUser))
              <div class="form-group row required">
                {{ Form::label('password', __('Password'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{-- Form::password('password', old('password'), ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'placeholder' => 'Password']) --}}
                  <input name="password" type="password" value="" id="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : ''}}" placeholder="Password">
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="form-group row required">
                {{ Form::label('password_confirmation', __('Confirm Password'), ['class' =>'col-sm-2 col-form-label']) }}
                <div class="col-sm-6">
                  {{-- Form::password('password_confirmation', old('password_confirmation'), ['class' => 'form-control' . ($errors->has('password_confirmation') ? ' is-invalid' : ''), 'placeholder' => 'Confirm Password']) --}}
                  <input name="password_confirmation" type="password" value="" id="password_confirmation" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : ''}}" placeholder="Confirm Password">
                  @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              @endif
              <div class="form-group row IndividualBox">
                {{ Form::label('dob', __('Date Of Birth'), ['class' =>'col-md-2 col-form-label text-md-left']) }}
                <div class="col-md-6">
                    {{ Form::date('dob', old('dob') ? old('dob') : ((isset($adminUser) && !empty($adminUser->dob)) ? $adminUser->dob->format('Y-m-d') : ''), ['class' => 'form-control' . ($errors->has('dob') ? ' is-invalid' : ''), 'placeholder' => 'Date Of Birth']) }}
                    @error('dob')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
              </div>

              <div class="form-group row required {{ $errors->has('role_id') ? 'has-error' : '' }}">
                {{ Form::label('role_id', __('Roles'), ['class' =>'col-md-2 col-form-label text-md-left']) }}
                <div class="col-md-6">
                    {{ Form::select('role_id', $roles->prepend('Select Role', ""), old("role_id") , ['class' => 'form-control roles'. ($errors->has('role_id') ? ' is-invalid' : '')]) }}
                    @error('role_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
        </div>
              
              <div class="form-group required {{ $errors->has('status') ? 'has-error' : '' }}">
                <div class="row">
                    {{ Form::label('status', __('Status'), ['class' =>'col-md-2 col-form-label text-md-left']) }}
                    <div class="col-md-6">
                        {{ Form::select('status', [1 => 'Active', 0 => 'Inactive'], old("status"), ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
              
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button type="submit" class="btn btn-info">Submit</button>
          <a href="{{ route('admin.admin-users.index') }}" class="btn btn-default float-right">Cancel</a>
        </div>
      </div>
      <!-- /.card -->
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.content -->
</x-slot>
</x-layouts.master>
