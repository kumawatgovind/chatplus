<x-layouts.admin>
@section('title','View Email Preference')
<x-slot name="breadcrumb">
    <div class="row mb-2">
    <div class="col-sm-12">
        <h1>Manage Email Preference</h1>
        <small>Here you can view email preference details</small>
    </div>
    <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.email-preferences.index'],['label' => 'View Email Preference Detail' ]]]) }}
    </div>
    </div>
</x-slot>
<x-slot name="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $emailPreference->title }}</h3>
            <a href="{{route('admin.email-preferences.index')}}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
        </div>
        <div class="card-body">
            <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Title') }}</th>
                    <td>{{ $emailPreference->title }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Layout Html') }}</th>
                    <td>{!! $emailPreference->layout_html !!}</td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $emailPreference->created_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $emailPreference->updated_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>

              
            </table>
            
        </div>
        <div class="card-footer">
            <a href="{{route('admin.email-preferences.index')}}" class="btn btn-default pull-left" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
        </div>
    </div>
</x-slot>
</x-layouts.admin>