<x-layouts.admin>
@section('title', 'Settings')
  <!-- Content Header (Setting header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Setting Manager</h1>
      </div>
      <div class="col-sm-6">
      {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.settings.index'],['label' => 'View Setting Detail']]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Setting Manager</h3>

            <div class="card-tools">
              <a href="{{ route('admin.settings.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
          <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Title') }}</th>
                    <td>{{ $setting->title }}</td>
                </tr>
          

                <tr>
                    <th scope="row">{{ __('Slug') }}</th>
                    <td>{{ $setting->slug }}</td>
                </tr>

              
                <tr>
                    <th scope="row">{{ __('Config') }}</th>
                    <td>{{ $setting->config_value }}</td>
                </tr>
               
                
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $setting->created_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $setting->updated_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>
                
            </table>
          </div>
   
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>