<x-layouts.admin>
@section('title','View Email Hook')
<x-slot name="breadcrumb">
    <div class="row mb-2">
    <div class="col-sm-6">
        <h1>Manage Email Hooks</h1>
        <small>Here you can view email hooks(slug) details</small>
    </div>
    <div class="col-sm-6">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.hooks.index'],['label' => 'View Email Hook Detail' ]]]) }}
    </div>
    </div>
</x-slot>
<x-slot name="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $email_hook->title }}</h3>
            <a href="{{route('admin.hooks.index')}}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
        </div>
        <div class="card-body">
            <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Title') }}</th>
                    <td>{{ $email_hook->title }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Hook/Slug') }}</th>
                    <td>{{ $email_hook->slug }}</td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $email_hook->created_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $email_hook->updated_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Status') }}</th>
                    <td>{{ $email_hook->status ? __('Active') : __('Inactive')  }}</td>
                </tr>
            </table>
            <div class="row">
                <div class="col-md-12">
                    <h4>{{ __('Description') }}</h4>
                    {{ $email_hook->description }}
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{route('admin.hooks.index')}}" class="btn btn-default pull-left" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
        </div>
    </div>
</x-slot>
</x-layouts.admin>