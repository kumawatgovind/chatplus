<x-layouts.admin>
@section('title','View Email Hook')
    <!-- Content Header (Manage Email Templates ) -->
    <x-slot name="breadcrumb">
        <div class="row mb-2">
        <div class="col-sm-12">
            <h1>Manage Email Templates</h1>
            <small>Here you can manage the email templates</small>
        </div>
        <div class="col-sm-12">
            {{ Breadcrumbs::render('common',['append' => [['label'=> 'Email Templates', 'route'=> 'admin.email-templates.index'],['label'=> 'View Email Templates Detail']]]) }}
        </div>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Email Template</h3>
            <a href="{{route('admin.email-templates.index')}}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
            <div class="card-body">
                <table class="table table-hover table-striped">
                    <tr>
                        <th scope="row">{{ __('Hook/Slug') }}</th>
                        <td>{{ $emailTemplate->email_hook->title }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ __('Subject') }}</th>
                        <td>{{ $message['subject'] }}</td>
                    </tr>
                    <tr>
                            <th scope="row">{{ __('Status') }}</th>
                            <td>{{ $emailTemplate->status == 1 ? "Active" : "Inactive" }}</td>
                        </tr>


                        <tr>
                            <th scope="row"><?= __('Created') ?></th>
                            <td>{{ $emailTemplate->created_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ __('Modified') }}</th>
                            <td>{{ $emailTemplate->updated_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                        </tr>
                </table>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ __('Email Template Layout') }}</h4>
                        {!! $message['message'] !!}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                    <a href="{{route('admin.email-templates.index')}}" class="btn btn-default pull-left" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
        </div>
    </x-slot>
</x-layouts.admin>