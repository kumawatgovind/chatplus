@php
$qparams = app('request')->query();
@endphp
<x-layouts.admin>
  @section('title', 'Service')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Service Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Service Manager', 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Service</h3>

            <div class="card-tools">
              <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm" title="Add  Service"><i class="fa fa-plus"></i> Add Service</a>
              <a href="{{ route('admin.categories.import') }}" class="btn btn-primary btn-sm" title="Import Sub Category"><i class="fa fa-plus"></i> Import Sub Category</a>
            </div>
          </div>
          <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                {{ Form::open(['url' => route('admin.categories.index', $qparams),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-3 form-group">
                    {{ Form::select('category_id', [-1 => 'Only Parent Category']+$parentCategories, app('request')->query('category_id'), ['class' => 'form-control','placeholder' => 'Select category']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Service Name']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
                  </div>
                </div>
                {{ Form::close() }}
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="width:7%">#</th>
                  <th scope="col" class="actions">Parent Service</th>
                  <th scope="col">@sortablelink('title', 'Service Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
              </thead>
              @if($categories->count() > 0)
              <tbody>
                @php
                $i = (($categories->currentPage() - 1) * ($categories->perPage()) + 1)
                @endphp
                @foreach($categories as $category)

                <tr class="row-{{ $category->id }}">
                  <td> {{$i}}. </td>
                  <td>
                    @if($category->parent)
                    {{ $category->parent->name }}
                    @else
                    Main Service
                    @endif
                  </td>
                  <td>{{$category->name}}</td>
                  <td>
                    @if ($category->status == 1)
                    <span class='btn btn-block btn-primary btn-xs updateStatus' data-value="1" data-column="status">Active</span>
                    @else
                    <span class='btn btn-block btn-danger btn-xs updateStatus' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $category->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.categories.show', [$category->id]) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View Service" title="View Service" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                    @if($category->parent_id == 0)
                    <a href="{{ route('admin.categories.edit', [$category->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit Service" title="Edit Service" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    @endif
                    <!-- 
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $category->title }}" title="Delete Category" data-action="delete" data-message="Are you sure want to delete this category?" data-url="{{ route('admin.categories.destroy', $category->id) }}" data-title="{{ $category->title }}"><i class="fa fa-trash"></i></a> -->
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
                  <td colspan='7' align='center'> <strong>Record Not Available</strong> </td>
                </tr>
              </tfoot>
              @endif
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer clearfix">
            {{ $categories->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>