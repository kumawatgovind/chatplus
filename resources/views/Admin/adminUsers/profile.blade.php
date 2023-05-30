<x-layouts.admin>
  @section('title', !empty($adminUser) ? 'Edit Admin User' : 'Add Admin User')
  
 <!-- Content Header (Page header) -->
<x-slot name="breadcrumb">
    <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Manage {{ $adminUser->first_name }} Profile</h1>
        </div>
        <div class="col-sm-6">
          {{ Breadcrumbs::render('common',['append' => [['label' => 'Manage '. $adminUser->first_name .' Profile' ]]]) }}
        </div>
      </div>
</x-slot>
<x-slot name="content">
  <!-- Main content -->

  {{ Form::model($adminUser, ['url' => route('admin.updateprofile'), 'method' => 'POST','files' => true]) }}
  
  <div class="row">
    <div class="col-md-12">
      <!-- SELECT2 EXAMPLE -->
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">Manage {{ $adminUser->first_name }} Profile</h3>
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
                  {{ Form::text('mobile', old('mobile'), ['class' => 'form-control' . ($errors->has('mobile') ? ' is-invalid' : ''), 'placeholder' => 'Mobile Number', 'maxlength' => 50]) }}
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
                  {{ Form::text('email', old('email'), ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email ID','disabled' => true]) }}
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
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
              
              <div class="form-group required {{ $errors->has('status') ? 'has-error' : '' }}">
                <div class="row">
                    {{ Form::label('status', __('Status'), ['class' =>'col-md-2 col-form-label text-md-left']) }}
                    <div class="col-md-6">
                        {{ Form::select('status', [1 => 'Active', 0 => 'Inactive'], old("status"), ['class' => 'form-control','disabled' => true]) }}
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
          <a href="{{ route('admin.dashboard') }}" class="btn btn-default float-right">Cancel</a>
        </div>
      </div>
      <!-- /.card -->
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.content -->
</x-slot>
@push('scripts')
<script>
//alert('I\'m coming from child')
</script>
@endpush
</x-layouts.admin>
