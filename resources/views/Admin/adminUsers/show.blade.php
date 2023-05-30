<x-layouts.master>

  <!-- Content Header (User header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin User Manager</h1>
      </div>
      <div class="col-sm-6">
      {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.admin-users.index'],['label' => 'View User Detail']]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin User Manager</h3>

            <div class="card-tools">
            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
          <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Name') }}</th>
                    <td>{{ $adminUser->first_name.' '.$adminUser->last_name }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Email') }}</th>
                    <td>{{ $adminUser->email }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Mobile') }}</th>
                    <td>{{ $adminUser->mobile }}</td>
                </tr>

                
                <tr>
                    <th scope="row">{{ __('Privilege') }}</th>
                    <td>{{ isset($roles[$adminUser->role_id])?$roles[$adminUser->role_id]:'NA' }}</td>
                </tr>

              

                <tr>
                    <th scope="row">{{ __('Status') }}</th>
                    <td>{{ $adminUser->status ? __('Active') : __('Inactive')  }}</td>
                </tr>
                
                
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $adminUser->created_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $adminUser->updated_at->format(config('get.ADMIN_DATE_TIME_FORMAT')) }}</td>
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
</x-layouts.master>