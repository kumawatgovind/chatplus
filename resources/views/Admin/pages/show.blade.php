<x-layouts.admin>
@section('title', 'CMS Pages')
  <!-- Content Header (Page header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Informative Content Manager</h1>
      </div>
      <div class="col-sm-6">
      {{ Breadcrumbs::render('common',['append' => [['label'=> 'Informative Content','route'=> 'admin.pages.index'],['label' => 'View Content Detail']]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Informative Content Manager</h3>

            <div class="card-tools">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
          <table class="table table-hover table-striped">
                <tr>
                    <th scope="row">{{ __('Title') }}</th>
                    <td>{{ $page->title }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Sub  Title') }}</th>
                    <td>{{ $page->sub_title == null ? 'NA' : $page->sub_title }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Slug') }}</th>
                    <td>{{ $page->slug }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Meta Title') }}</th>
                    <td>{{ $page->meta_title == null ? 'NA' : $page->meta_title }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Meta Keywords') }}</th>
                    <td>{{ $page->meta_keyword == null ? 'NA' : $page->meta_keyword }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Meta Description') }}</th>
                    <td class="editor-content">{{ $page->meta_description == null ? 'NA' : $page->meta_description }}</td>
                </tr>

                <tr>
                    <th scope="row">{{ __('Status') }}</th>
                    <td>{{ $page->status ? __('Active') : __('Inactive')  }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Description') }}</th>
                    <td class="editor-content">{!! $page->description  !!}</td>
                </tr>
                
                <tr>
                    <th scope="row"><?= __('Created') ?></th>
                    <td>{{ $page->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ __('Modified') }}</th>
                    <td>{{ $page->updated_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
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