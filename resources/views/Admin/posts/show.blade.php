<style type="text/css">
  img {
    max-width: 100%;
  }

  .gallery-pic {
    position: relative;
    height: 250px;
    overflow: hidden;

  }

  .delete-icon {
    position: absolute;
    right: 0;
    top: 0;
    background: #ff2000;
    width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    font-style: normal;
    padding: 4px 0 0 0;
  }

  .row.gallery-sec [class*="col-"] {
    margin-bottom: 30px;
  }
</style>
<x-layouts.admin>
  @section('title', 'Post')
  <!-- Content Header (Location header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Post Manager</h1>
      </div>
      <div class="col-sm-6">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Posts','route'=> 'admin.posts.index'],['label' => 'View Gallery Detail']]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Post Manager</h3>

            <div class="card-tools">
              <a href="{{ route('admin.posts.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="box-body">
              <table class="table table-hover table-striped">
                <tr>
                  <th scope="row">{{ __('Status') }}</th>
                  <td>
                    @if ($post->status == 1)
                        <span style="width: 200px;" class='btn1 btn-block btn-success btn-xs updateStatus text-center' data-value="1" data-column="status">Active</span>
                    @else
                        <span style="width: 200px;"  class='btn1 btn-block btn-danger btn-xs updateStatus text-center' data-value="0">In-Active</span>
                    @endif
                  </td>
                </tr>
                
                <tr>
                  <th scope="row">{{ __('description') }}</th>
                  <td>{{ $post->description }}</td>
                </tr>
              </table>
            </div><!-- /.box-body -->
            @if(!empty($post->attachments))
            <div class="row gallery-sec">
              @foreach($post->attachments as $attachment)
              <div class="col-md-3">
                <div class="gallery-pic">
                  @if(\Storage::disk('s3')->exists('posts/'.$attachment->name))
                    <img src="{{ $attachment->url }}">
                    
                  @else
                    <img src="{{ url('img/no-icon.jpg') }}">
                  @endif
                    <a href="javascript:void(0);" class="confirmDeleteBtn  btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete Image" title="Delete Image" data-url="{{ route('admin.posts.deleteAttachment', $attachment->id) }}" data-action="delete" data-message="Are you sure want to delete this attachment?">
                      <i class="fa fa-trash"></i>
                    </a>
                </div>
              </div>
              @endforeach
            </div>
            @endif
          </div>

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>