<x-layouts.admin>
  @section('title', 'Service')
  <!-- Content Header (Category header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Service Manager</h1>
      </div>
      <div class="col-sm-6">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.categories.index'],['label' => 'View  Service Detail']]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Service Manager</h3>

            <div class="card-tools">
              <a href="{{ route('admin.categories.index') }}" class="btn btn-default pull-right" title="Back"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>

            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-hover table-striped">
              <tr>
                <th scope="row">{{ __('Title') }}</th>
                <td>{{ $category->name }}</td>
              </tr>
              @if($category->slug)
              <tr>
                <th scope="row">{{ __('Slug') }}</th>
                <td>{{ $category->slug }}</td>
              </tr>
              @endif
              @if($category->parent)
              <tr>
                <th scope="row">{{ __('Parent Category') }}</th>
                <td>{{ $category->parent->name }}</td>
              </tr>
              @endif

              <tr>
                <th scope="row">{{ __('Status') }}</th>
                <td>{{ $category->status ? __('Active') : __('Inactive')  }}</td>
              </tr>

              <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td>{{ $category->created_at ? $category->created_at->format(config('get.ADMIN_DATE_FORMAT')) : '' }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Modified') }}</th>
                <td>{{ $category->updated_at ? $category->updated_at->format(config('get.ADMIN_DATE_FORMAT')) : '' }}</td>
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