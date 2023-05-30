@php
 $qparams = app('request')->query();
@endphp
<x-layouts.admin>
@section('title', 'Tags')
  <!-- Content Header (Tags header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Tags Manager</h1>
      </div>
      <div class="col-sm-6">
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
            <h3 class="card-title">Admin Tags</h3>

            <div class="card-tools">
              <a href="{{ route('admin.tags.create') }}" class="btn btn-block btn-primary btn-sm" title="Add  Tag"><i class="fa fa-plus"></i> Add Tag</a>
            </div>
          </div>
          <div class="card-body">
              <div class="filter-outer box box-info">
                <div class="box-header">
                  <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                  {{ Form::open(['url' => route('admin.tags.index', $qparams),'method' => 'get']) }}
                    <div class="row">
                      <div class="col-md-5 form-group">
                        {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Tag Name']) }}
                      </div>
                      <div class="col-md-3 form-group">
                        <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.tags.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">@sortablelink('title', 'Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>Related Post Count</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
              </thead>
              @if($tags->count() > 0)
              <tbody>
                @php
                $i = (($tags->currentPage() - 1) * ($tags->perPage()) + 1)
                @endphp
                @foreach($tags as $tag)
                <tr class="row-{{ $tag->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$tag->name}}</td>
                  <td>({{ $tag->mentioned_posts_count }})</td>
                  <td>
                     @if ($tag->status == 1)
                        <span class='btn btn-block btn-primary btn-xs updateStatus' data-value="1" data-column="status">Active</span>
                    @else
                        <span class='btn btn-block btn-danger btn-xs updateStatus' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $tag->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.tags.edit',[ $tag->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit Tag" title="Edit tag" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $tag->title }}" title="Delete Tag" data-action="delete" data-message="Are you sure want to delete this tag?" data-url="{{ route('admin.tags.destroy', $tag->id) }}" data-title="{{ $tag->name }}"><i class="fa fa-trash"></i></a>
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
          {{ $tags->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>