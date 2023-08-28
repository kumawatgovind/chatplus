<?php

use Illuminate\Support\Str;

$title = "User Manager";
$add = "User";
?>
<x-layouts.admin>
  @section('title', $title)
  <!-- Content Header (Customers header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>{{ $title }}</h1>
      </div>
      <div class="col-sm-12">

        {{ Breadcrumbs::render('common',['append' => [['label'=> $title ,'url'=> url()->previous()],['label' => !empty($user) ? 'Edit '.$add : 'Add '.$add ]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>

            <div class="card-tools">
              <a href="{{ route('admin.users.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          @if(isset($user))
          {{ Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'patch', 'id' => 'submit-user']) }}
          @else
          {{ Form::open(['route' => ['admin.users.store'], 'id' => 'submit-user']) }}
          {{ Form::hidden('random', Str::random(8)) }}
          @endif
          <div class="card-body">
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Name</label>
                    {{ Form::text('name', old('name'), ['class' => 'form-control','placeholder' => 'Name', 'readonly' => true]) }}
                    @if($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group  {{ $errors->has('username') ? 'has-error' : '' }}">
                    <label for="username">User Name</label>
                    {{ Form::text('username', old('username'), ['class' => 'form-control','placeholder' => 'User Name', 'readonly' => true]) }}
                    @if($errors->has('username'))
                    <span class="help-block">{{ $errors->first('username') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('country_code') ? 'has-error' : '' }}">
                    <label for="country_code">Country code</label>
                    {{ Form::text('country_code', old('country_code'), ['class' => 'form-control','placeholder' => 'Country code', 'readonly' => true]) }}
                    @if($errors->has('country_code'))
                    <span class="help-block">{{ $errors->first('country_code') }}</span>
                    @endif
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group  {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                    <label for="phone_number">Phone number</label>
                    {{ Form::text('phone_number', old('phone_number'), ['class' => 'form-control','placeholder' => 'Phone number', 'readonly' => true]) }}
                    @if($errors->has('phone_number'))
                    <span class="help-block">{{ $errors->first('phone_number') }}</span>
                    @endif
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Email</label>
                    {{ Form::email('email', old('email'), ['class' => 'form-control','placeholder' => 'Email', 'readonly' => true]) }}
                    @if($errors->has('email'))
                    <span class="help-block">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="chk-container">Is Active
                      @if(isset($user))
                      @if($user->status == 1)
                      <input name="status" type="checkbox" checked="checked">
                      @else
                      <input name="status" type="checkbox">
                      @endif
                      @else
                      <input name="status" type="checkbox" checked="checked">
                      @endif
                      <span class="checkmark"></span>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="chk-container">Is Block
                    @if(isset($user))
                    @if($user->is_block == 1)
                    <input name="is_block" type="checkbox" checked="checked">
                    @else
                    <input name="is_block" type="checkbox">
                    @endif
                    @else
                    <input name="is_block" type="checkbox" checked="checked">
                    @endif
                    <span class="checkmark"></span>
                  </label>
                </div>
              </div>
            </div>

          </div><!-- /.box-body -->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
        </div>
        {{ Form::close() }}
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.content -->
    @push('scripts')
    <script type="text/javascript">
      $(document).ready(function() {
        $("form").submit(function(event) {
          $('.submit-form').attr('disabled', 'disabled');
        });
      });
    </script>
    @endpush
  </x-slot>
</x-layouts.admin>