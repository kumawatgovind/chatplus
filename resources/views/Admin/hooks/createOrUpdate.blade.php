<x-layouts.admin>
@section('title', !empty($emailHook) ? 'Edit Email Hook' : 'Add Email Hook')
<x-slot name="breadcrumb">
    <div class="row mb-2">
    <div class="col-sm-12">
        <h1>Manage Email Hooks</h1>
        <small>Here you can {{ !empty($emailHook) ? 'edit' : 'add' }} email hook(slug)</small>
    </div>
    <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.hooks.index'],['label' => !empty($emailHook) ? 'Edit Email Hooks' : 'Add Email Hooks' ]]]) }}
    </div>
    </div>
</x-slot>

<x-slot name="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary1 emailHooks">
                <div class="card-header with-border">
                    <h3 class="card-title">{{ !empty($emailHook) ? 'Edit Email Hook' : 'Add Email Hook' }} </h3>
                    <a href="{{ route('admin.hooks.index') }}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                </div><!-- /.card-header -->
                @if(isset($emailHook))
                    {{ Form::model($emailHook, ['route' => ['admin.hooks.update', $emailHook->id], 'method' => 'patch']) }}
                @else
                    {{ Form::open(['route' => 'admin.hooks.store']) }}
                @endif

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
                                <label for="title">Title</label>
                                {{ Form::text('title', old('title'), ['class' => 'form-control','placeholder' => 'Title']) }}
                                @if($errors->has('title'))
                                <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif

                              </div>
                            <div class="form-group required {{ $errors->has('slug') ? 'has-error' : '' }}">
                                <label for="slug">Hook</label>
                                {{ Form::text('slug', old('slug'), ['class' => 'form-control','placeholder' => 'Hook/Slug' ,'readonly' => isset($emailHook) ? true : false]) }}
                                @if($errors->has('slug'))
                                <span class="help-block">{{ $errors->first('slug') }}</span>
                                @endif
                            </div>
                            <p class="help-block"><small>No space, separate each word with underscore. (if you want auto generated then please leave blank)</small></p>
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
                <div class="card-footer">
                    <button class="btn btn-primary btn-flat" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
                    <a href="{{ route('admin.hooks.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-primary1">
                <div class="card-header with-border">
                    <h3 class="card-title"><i class="fa fa-anchor"></i> Last updated email hooks</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- form start -->
                    <!-- form start -->
                    @if (!$emailHookLists->isEmpty())
                        <div class="timeline card">
                            <!-- timeline time label -->
                            @foreach ($emailHookLists as $hook)
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
                                        <a href="{{ route('admin.hooks.show', $hook->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-fw fa-eye"></i></a>
                                        <a href="{{ route('admin.hooks.edit', $hook->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                    </div>
                                    </div>
                                </div>
                                <!-- END timeline item -->
                           @endforeach
                        </div>
                    @endif
                </div>

            </div>
            <!-- /.box -->
        </div>
    </div>
</x-slot>
</x-layouts.admin>