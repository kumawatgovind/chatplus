<x-layouts.admin>
@section('title', !empty($emailPreference) ? 'Edit Email Hook' : 'Add Email Hook')
<x-slot name="breadcrumb">
    <div class="row mb-2">
    <div class="col-sm-12">
        <h1>Manage Email Preference</h1>
        <small>Here you can {{ !empty($emailPreference) ? 'edit' : 'add' }} email preferences(layout)</small>
    </div>
    <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.email-preferences.index'],['label' => !empty($emailPreference) ? 'Edit Email Preference' : 'Add Email Preference' ]]]) }}
    </div>
    </div>
</x-slot>
<x-slot name="content">
    <div class="row">
        <div class="col-md-7">
            <div class="card emailPreferences">
                <div class="card-header with-border">
                    <h3 class="card-title">{{ !empty($emailPreference) ? 'Edit Email Preference' : 'Add Email Preference' }} </h3>
                    <a href="{{ route('admin.email-preferences.index') }}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                </div><!-- /.card-header -->
                @if(isset($emailPreference))
                    {{ Form::model($emailPreference, ['route' => ['admin.email-preferences.update', $emailPreference->id], 'method' => 'patch']) }}
                @else
                    {{ Form::open(['route' => 'admin.email-preferences.store']) }}
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
                              
                            <div class="form-group required {{ $errors->has('layout_html') ? 'has-error' : '' }}">
                                <label for="description">Layout Html</label>
                                {{ Form::textarea('layout_html', old('layout_html'), ['class' => 'form-control','placeholder' => 'Layout Html', 'rows' => 8]) }}
                                @if($errors->has('layout_html'))
                                <span class="help-block">{{ $errors->first('layout_html') }}</span>
                                @endif
                            </div>     
                        </div>
                    </div> <!-- /.row -->
                </div><!-- /.box-body -->
                <div class="card-footer">
                    <button class="btn btn-primary btn-flat" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>  
                    <a href="{{ route('admin.email-preferences.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>                
                </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="col-md-5">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation"></i> Important Rules
                        </h3>
    
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <p>
                            For each email style or email preference that would be added to the system, make sure it has these hooks:
                        </p>
                        <ul>
                            <li>
                                <small class="label bg-yellow">
                                    ##SYSTEM_LOGO##
                                </small> - Will be replaced by logo from the admin settings.
                            </li>
                            <li>
                                <small class="label bg-yellow">
                                    ##SYSTEM_APPLICATION_NAME##
                                </small> - Will be replaced by application name from admin settings.
                            </li>
                            <li>
                                <small class="label bg-yellow">
                                    ##EMAIL_CONTENT##
                                </small> - Will be replaced by email message from email hook settings.
                            </li>
                            <li>
                                <small class="label bg-yellow">
                                    ##EMAIL_FOOTER##
                                </small> - Will be replaced by email footer from email hook settings.
                            </li>
                        </ul>
                    </div><!-- ./box-body -->
                </div>
            </div>
    </div>
</x-slot>
</x-layouts.admin>