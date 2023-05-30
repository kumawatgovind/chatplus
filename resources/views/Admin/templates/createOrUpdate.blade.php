<x-layouts.admin>
@section('title', !empty($emailTemplate) ? 'Edit Email Template' : 'Add Email Template')
<x-slot name="breadcrumb">
    <div class="row mb-2">
    <div class="col-sm-12">
        <h1>Manage Email Templates</h1>
        <small>Here you can {{ !empty($emailTemplate) ? 'edit' : 'add' }} email templates</small>
    </div>
    <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Email Template', 'route'=> 'admin.email-templates.index'],['label' => !empty($emailTemplate) ? 'Edit Email Template' : 'Add Email Template' ]]]) }}
    </div>
    </div>
</x-slot>
<x-slot name="content">
    <div class="row">
        <div class="col-md-7">
            <div class="card emailTemplates">
                <div class="card-header">
                    <h3 class="card-title">{{ !empty($emailTemplate) ? 'Edit Email Template' : 'Add Email Template' }} </h3>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                </div><!-- /.card-header -->
                @if(isset($emailTemplate))
                    {{ Form::model($emailTemplate, ['route' => ['admin.email-templates.update', $emailTemplate->id], 'method' => 'patch']) }}
                @else
                    {{ Form::open(['route' => 'admin.email-templates.store']) }}
                @endif
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group required {{ $errors->has('email_hook_id') ? 'has-error' : '' }}">
                                <label for="title">Email Hook</label>
                                {{ Form::select('email_hook_id', $hooks, null , ['class' => 'form-control', 'placeholder' => 'Select Email Hook']) }}
                                @if($errors->has('email_hook_id'))
                                <span class="help-block">{{ $errors->first('email_hook_id') }}</span>
                                @endif
                              </div>

                            <div class="form-group required {{ $errors->has('subject') ? 'has-error' : '' }}">
                                <label for="title">Subject</label>
                                {{ Form::text('subject', old('subject'), ['class' => 'form-control','placeholder' => 'Subject']) }}
                                @if($errors->has('subject'))
                                <span class="help-block">{{ $errors->first('subject') }}</span>
                                @endif
                              </div>


                            <div class="form-group required {{ $errors->has('description') ? 'has-error' : '' }}">
                                <label for="description">Description (Email Content)</label>
                                {{ Form::textarea('description', old('description'), ['class' => 'form-control ckeditor','placeholder' => 'Description', 'rows' => 8]) }}
                                @if($errors->has('description'))
                                <span class="help-block">{{ $errors->first('description') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="description">Footer Text</label>
                                {{ Form::textarea('footer_text', old('footer_text'), ['class' => 'form-control','placeholder' => 'Footer Text', 'rows' => 8]) }}
                            </div>


                            <div class="form-group required {{ $errors->has('email_preference_id') ? 'has-error' : '' }}">
                                <label for="title">Email Layout</label>
                                {{ Form::select('email_preference_id', $emailPreference, null , ['class' => 'form-control', 'placeholder' => 'Select Layout']) }}
                                @if($errors->has('email_preference_id'))
                                <span class="help-block">{{ $errors->first('email_preference_id') }}</span>
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
                        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                    </div>
                    {{ Form::close() }}
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">
                        <i class="fa fa-exclamation"></i> Important Rules
                    </h3>

                </div><!-- /.card-header -->
                <div class="card-body">
                <p>For each email hook that would be added to the sytem, make sure to follow these rules:</p>
                <ul>
                    <li>
                        Use <small class="label bg-yellow">##SYSTEM_APPLICATION_NAME##</small>
                        on the subject or message to print application name defined by admin settings.
                    </li>
                    <li>
                        Use <small class="label bg-yellow">##USER_EMAIL##</small>
                        on the subject or message to print user email.
                    </li>
                    <li>
                        Use <small class="label bg-yellow">##USER_NAME##</small>
                        on the subject or message to print user name.
                    </li>
                    <li>Make sure the message contain <small class="label bg-yellow">##MESSAGE##</small>.</li>
                </ul>
            </div>
            </div>


            @if (!$emailTemplateLists->isEmpty())
            <div class="card card-default">
                    <div class="card-header with-border">
                        <h3 class="card-title"><i class="fa fa-anchor"></i> Last updated templates</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- form start -->
                            <div class="timeline">
                                <!-- timeline time label -->
                                @foreach ($emailTemplateLists as $emailTemplate)
                                        <!-- timeline time label -->
                                        <div class="time-label">
                                            <span class="bg-navy">
                                                {{ $emailTemplate->created_at->toFormattedDateString() }}
                                            </span>
                                        </div>
                                        <!-- /.timeline-label -->
                                        <!-- timeline item -->
                                        <div>
                                            <i class="fa fa-envelope bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i> {{ $emailTemplate->created_at->format('H:i A') }}</span>

                                                <h3 class="timeline-header">
                                                   <a href="{{ route('admin.hooks.index') }}/{{ $emailTemplate->email_hook->id }}"> {{ $emailTemplate->email_hook->title }} ({{ $emailTemplate->email_hook->slug }}) </a>
                                                </h3>
                                                <div class="timeline-body">
                                                    <h3 style="margin-top: 0px;"> <small>
                                                            <a href="{{ route('admin.email-preferences.show', $emailTemplate->email_preference->id) }}">{{ $emailTemplate->email_preference->title }}</a>
                                                        </small>
                                                    </h3>
                                                    {{ $emailTemplate->subject }}
                                                </div>
                                                <div class="timeline-footer">
                                                    <a href="{{ route('admin.email-templates.show', $emailTemplate->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a href="{{ route('admin.email-templates.edit', $emailTemplate->id) }}" class="btn btn-primary btn-xs btn-flat"><i class="fa fa-edit"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- END timeline item -->
                                @endforeach
                            </div>
                    </div>

            </div>
            @endif
    </div>
</x-slot>
</x-layouts.admin>