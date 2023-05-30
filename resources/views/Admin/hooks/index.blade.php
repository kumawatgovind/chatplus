<x-layouts.admin>
@section('title','Email Hooks')
    <!-- Content Header (Manage Email Hooks) -->
    <x-slot name="breadcrumb">
        <div class="row mb-2">
        <div class="col-sm-12">
            <h1>Manage Email Hooks</h1>
        </div>
        <div class="col-sm-12">
            {{ Breadcrumbs::render('common',['append' => [['label'=> $getController, 'route'=> \Request::route()->getName()]]]) }}
        </div>
        </div>
    </x-slot>
<?php
  $response = Gate::inspect('check-user', "email_templates-create");
  $canCreate = true;
  if (!$response->allowed()) {
      $canCreate = false;
  }
  ?>
    <x-slot name="content">
        {{-- <div class="card-header">
            <div class="card-tools">
            <h3 class="card-title">List Email Hooks</h3>
            @if($canCreate)
                <a href="{{ route('admin.hooks.create') }}" class="btn btn-block btn-primary btn-sm  float-right"><i class="fa fa-plus"></i> New Email Hooks</a>
            @endif
            </div>
        </div> --}}
        <div class="row emailHooks">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List Email Hooks</h3>
                        <div class="card-tools">
                        @if($canCreate)
                            <a href="{{ route('admin.hooks.create') }}" class="btn btn-primary float-right" title="Add Email Hooks"><i class="fa fa-plus"></i> New Email Hooks</a>
                        @endif
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body table-responsive">
                    @if (!$emailHooks->isEmpty())
                    <div class="timeline">
                        @foreach ($emailHooks as $hook)
                        <!-- timeline time label -->
                        <div class="time-label">
                            <span class="bg-navy">
                                {{ $hook->created_at->toFormattedDateString() }}
                            </span>
                        </div>
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <div>
                            <i class="fas fa-envelope bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $hook->created_at->format('H:i A') }}</span>
                                <h3 class="timeline-header">{{ $hook->title }}</h3>

                                <div class="timeline-body">
                                    {{ $hook->description }}
                                </div>
                                <div class="timeline-footer">
                                    <a href="{{ route('admin.hooks.show', $hook->id) }}" class="btn btn-warning btn-xs" title="View Email Hooks"><i class="fa fa-fw fa-eye"></i></a>
                                    @if($canCreate)
                                        <a href="{{ route('admin.hooks.edit', $hook->id) }}" class="btn btn-primary btn-xs" title="Edit Email Hooks"><i class="fa fa-edit"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- END timeline item -->
                        @endforeach
                    </div>
                    @else
                        <div style="align:center;">  <strong>Record Not Available</strong> </div>
                    @endif
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card card-default">
                    <div class="card-header with-border">
                        <h3 class="card-title"><i class="fa fa-anchor"></i> Quick Start</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    {{ Form::open(['route' => 'admin.hooks.store']) }}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
                                        <label for="title">Title</label>
                                        {{ Form::text('title', old('title'), ['class' => 'form-control','placeholder' => 'Title']) }}
                                        @if($errors->has('title'))
                                        <span class="help-block">{{ $errors->first('title') }}</span>
                                        @endif

                                        </div>
                                    <div class="form-group">
                                        <label for="slug">Hook</label>
                                        {{ Form::text('slug', old('slug'), ['class' => 'form-control','placeholder' => 'Hook/Slug' ,'readonly' => isset($emailHook) ? true : false]) }}
                                        <p class="help-block">No space, separate each word with underscore. (if you want auto generated then please leave blank)</p>
                                    </div>
                                    <div class="form-group required {{ $errors->has('description') ? 'has-error' : '' }}">
                                        <label for="description">Description</label>
                                        {{ Form::textarea('description', old('description'), ['class' => 'form-control','placeholder' => 'Description', 'rows' => 8]) }}
                                        {{-- <textarea name="description" class="form-control" placeholder="Description" required="required" id="description" rows="5">{{ old("description") ? old("description") : (!empty($emailHook) ? $emailHook->description : '') }}</textarea> --}}
                                        @if($errors->has('description'))
                                        <span class="help-block">{{ $errors->first('description') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        {{ Form::select('status', [1 => 'Active', 0 => 'Inactive'], old("status"), ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div> <!-- /.row -->
                        </div><!-- /.card-body -->
                        @if($canCreate)
                            <div class="card-footer">
                                <button class="btn btn-primary l-button" data-size="xs" title="Submit" type="submit"><span class="ladda-label"><i class="fa fa-fw fa-save"></i> Submit</span></button>
                            </div>
                        @endif
                        {{ Form::close() }}
                </div>
            </div>
        </div>
    </x-slot>
</x-layouts.admin>
