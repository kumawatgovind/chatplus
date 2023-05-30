<x-layouts.admin>
@section('title', 'Posts')
  
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Admin Post Manager</h1>
        <small>Before Add new Post filter by user.</small>
      </div>
      <div class="col-sm-12">
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
            <h3 class="card-title">Admin Posts</h3>

            <div class="card-tools">
              <a href="{{ route('admin.posts.create', ['u' => app('request')->query('u')]) }}" class="btn btn-block btn-primary btn-sm" title="Add Post"><i class="fa fa-plus"></i> Add Post</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
              <div class="filter-outer box box-info">
                <div class="box-header">
                  <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                </div>
                <div class="box-body">
                  {{ Form::open(['url' => route('admin.posts.index'),'method' => 'get']) }}
                    <div class="row">
                      <div class="col-md-5 form-group">
                        {{ Form::select('u', $users, app('request')->query('u'), ['class' => 'form-control','placeholder' => 'Select Users']) }}
                      </div>
                      <div class="col-md-3 form-group">
                        <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">Category</th>
                  <th scope="col">Profile Name</th>
                  <th scope="col">Attachment</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
                
              </thead>
              @if($posts->count() > 0)
              <tbody>
                @php
                $i = (($posts->currentPage() - 1) * ($posts->perPage()) + 1)
                @endphp
                @foreach($posts as $post)
              
                <tr class="row-{{ $post->id }}">
                  <td> {{$i}}. </td>
                  <td>{{($post->category) ? $post->category->title : ''}}</td>
                  <td>{{ $post->user->username??'' }}</td>
                  <td>{{ $post->attachments_count }}</td>
                  <td>
                    @if ($post->status == 1)
                        <span class='btn1 btn-block btn-success btn-xs updateStatus text-center' data-value="1" data-column="status">Active</span>
                    @else
                        <span class='btn1 btn-block btn-danger btn-xs updateStatus text-center' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $post->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View Post" title="View Post" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit" title="Edit Post" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $post->title }}" title="Delete Post" data-url="{{ route('admin.posts.destroy', $post->id) }}" data-title="{{ $post->title }}"  data-action="delete" data-message="Are you sure want to delete this post?"><i class="fa fa-trash"></i></a>
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
          {{ $posts->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>