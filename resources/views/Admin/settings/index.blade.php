<x-layouts.admin>
  @section('title', 'Settings')
 <!-- Content Header (Page header) -->
<x-slot name="breadcrumb">
    <div class="row mb-2">
        <div class="col-sm-12">
          <h1>Global Settings</h1>
        </div>
        <div class="col-sm-12">
          {{-- <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Simple Tables</li>
          </ol> --}}
          {{ Breadcrumbs::render('common',['append' => [['label'=> $getController, 'route'=> \Request::route()->getName()]]]) }}
        </div>
      </div>
</x-slot>

<x-slot name="content">
  <!-- Main content -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Settings</h3>

          <div class="card-tools">
                <a href="{{ route('admin.settings.create') }}" class="btn btn-block btn-primary btn-sm" title="Add Setting"><i class="fa fa-plus"></i> Add Settings</a>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0"> 
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width: 7%">#</th>
                <th>@sortablelink('title', 'Title')</th>
                <th>@sortablelink('slug', 'Constant/Slug')</th>
                <th>@sortablelink('config_value', 'Value')</th>
                <th scope="col" class="actions" width="12%">Action</th>
              </tr>
            </thead>
            @if($settings->count() > 0)
            <tbody>
                @php
                $i = (($settings->currentPage() - 1) * ($settings->perPage()) + 1)
                @endphp
                @foreach($settings as $setting)
                  <tr class="row-{{ $setting->id }}">
                      <td> {{ $i }}. </td>
                      <td>{{ $setting->title }}</td>
                      <td>{{ $setting->slug }}</td>
                      <td>{{ $setting->config_value }}</td>
                      <td class="actions">
                          @php
                              $queryStr['id'] = $setting->id;
                              $queryStr = array_merge( $queryStr , app('request')->query());
                          @endphp
                          <div class="form-group">
                              <a href="{{ route('admin.settings.show', $setting->id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View Setting Detail" title="View Setting Detail" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                              <a href="{{ route('admin.settings.edit', $setting->id) }}" class="btn btn-success btn-xs" data-toggle="tooltip" alt="Edit" title="Edit Setting" data-original-title="Edit Setting"><i class="fa fa-edit"></i></a>
                              <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $setting->name }}" title="Delete Setting" data-url="{{ route('admin.settings.destroy', $setting->id) }}" data-title="{{ $setting->name }}"><i class="fa fa-trash"></i></a>
                          </div>
                      </td>
                  </tr>
                @php
                    $i++;
                @endphp
                @endforeach
            </tbody>
            @else
            <tfoot>
                <tr>
                    <td colspan='10' align='center'> <strong>No records available</strong> </td>
                </tr>
            </tfoot>
            @endif
          </table>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          {{-- <ul class="pagination pagination-sm m-0 float-right">
            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
          </ul> --}}
          {!! $settings->appends(\Request::except('page'))->render() !!}
        </div>
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.content -->
</x-slot>
</x-layouts.admin>
