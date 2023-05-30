<x-layouts.admin>
@section('title','Email preferences')
    <!-- Content Header (Manage Email Preference) -->
    <x-slot name="breadcrumb">
        <div class="row mb-2">
        <div class="col-sm-12">
            <h1>Manage Email Preference</h1>
            <small>Here you can manage the email preferences(layouts)</small>
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
        <div class="row emailHooks">
            <div class="col-md-7">
                <div class=" card">
                    <div class="card-header">
                        <h3 class="card-title"><span class="caption-subject font-green bold uppercase">{{ __('List Email Preferences') }}</span></h3>
                        <div class="card-tools">
                            @if($canCreate)
                                <a href="{{route('admin.email-preferences.create')}}" class="btn btn-success btn-flat" title="Add Preferences"><i class="fa fa-plus"></i>  New Email Preference</a>
                            @endif
                        </div>
                    </div><!-- /.card-header -->
                    <div class="card-body table-responsive">
                     @if (!$emailPreferences->isEmpty())
                            <div class="timeline">
                               @foreach ($emailPreferences as $emailPreference)
                                    <!-- timeline time label -->
                                    <div class="time-label">
                                        <span class="bg-navy">
                                            {{ $emailPreference->created_at->toFormattedDateString() }}
                                        </span>
                                    </div>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-envelope bg-blue"></i>
                                        <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $emailPreference->created_at->format('H:i A') }}</span>
                                        <h3 class="timeline-header">{{ $emailPreference->title }}</h3>
                                        <div class="timeline-footer">
                                            <a href="{{ route('admin.email-preferences.show', $emailPreference->id) }}" class="btn btn-warning btn-xs" title="View Preferences"><i class="fa fa-fw fa-eye"></i></a>
                                            @if($canCreate)
                                                <a href="{{ route('admin.email-preferences.edit', $emailPreference->id) }}" class="btn btn-primary btn-xs" title="Edit Preferences"><i class="fa fa-edit"></i></a>
                                            @endif
                                        </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                @endforeach
                            </div>
                            @else
                            <div style="align:center;"><strong>Record Not Available</strong></div>
                            @endif
                    </div>

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