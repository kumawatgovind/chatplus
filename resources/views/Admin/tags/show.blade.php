<x-layouts.admin>
@section('title', 'Tags')
  <!-- Content Header (Tags header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Tags Manager</h1>
      </div>
      <div class="col-sm-6">
      {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.tags.index'],['label' => 'View Tags Detail']]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Tags Manager</h3>

            <div class="card-tools">
            <a href="{{ route('admin.tags.index') }}" class="btn btn-default pull-right" title="Back"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
              
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
          <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Title') }}</th>
                    <td>{{ $tag->name }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Related Post Count') }}</th>
                    <td>({{ $tag->mentionedPosts()->count() }})</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Status') }}</th>
                    <td>{{ $tag->status ? __('Active') : __('Inactive')  }}</td>
                </tr>
               
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $tag->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $tag->updated_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
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