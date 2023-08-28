<style>
.admin_pagination svg{
  width: 20px;
}

.admin_pagination .flex-1 
{
  display: none;
}

</style>
<x-layouts.admin>
@php
 $qparams = app('request')->query();
@endphp
@section('title', 'Informative Content')
  <!-- Content Header (Page header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Admin CMS Pages Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Pages', 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>
<?php
  $response = Gate::inspect('check-user', "pages-create");
  $canCreate = true;
  if (!$response->allowed()) {
      $canCreate = false;
  }
  ?>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin CMS Pages</h3>
            @if($canCreate)
            <div class="card-tools">
            <a href="{{ route('admin.pages.create') }}" class="btn btn-block btn-primary btn-sm" title="Add Content"><i class="fa fa-plus"></i> Add Content</a>
            </div>
             @endif
          </div>
          <!-- /.card-header -->
          <div class="card-body">
              <div class="filter-outer box box-info">
                <div class="box-header">
                  <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                </div>
                <div class="box-body">
                  {{ Form::open(['url' => route('admin.pages.index', $qparams),'method' => 'get']) }}
                    <div class="row">
                      <div class="col-md-5 form-group">
                        {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Title, Slug']) }}
                      </div>
                      <div class="col-md-3 form-group">
                        <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
                      </div>
                    </div>
                 
                  {{ Form::close() }}
                </div>
              </div>
          </div>
          <div class="card-body table-responsive p-0"> 
            <table class="table table-bordered table-hover">
              <thead>
                
                <tr>
                  <th style="width:7%">#</th>
                  <th scope="col">@sortablelink('title', 'Title', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">@sortablelink('slug', 'Slug', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
                
              </thead>
              @if($pages->count() > 0)
              <tbody>
                @php
                $i = (($pages->currentPage() - 1) * ($pages->perPage()) + 1)
                @endphp
                @foreach($pages as $page)
                <tr class="row-{{ $page->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$page->title}}</td>
                  <td>{{$page->slug}}</td>
                  <td>
                    @if ($page->status == 1)
                        <span class='btn1 btn-block btn-success btn-xs updateStatus text-center' data-value="1" data-column="status">Active</span>
                    @else
                        <span class='btn1 btn-block btn-danger btn-xs updateStatus text-center' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $page->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.pages.show',['page' => $page->id]) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View setting" title="View Content" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>

                    @if($canCreate)
                    <a href="{{ route('admin.pages.edit',['page' => $page->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit" title="Edit Content" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $page->name }}" title="Delete Content" data-url="{{ route('admin.pages.destroy', $page->id) }}" data-title="{{ $page->name }}"  data-action="delete" data-message="Are you sure want to delete this cms page?"><i class="fa fa-trash"></i></a>
                    @endif
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
          {{ $pages->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>