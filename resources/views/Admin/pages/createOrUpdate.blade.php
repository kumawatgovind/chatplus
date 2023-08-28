<x-layouts.admin>
@section('title', 'CMS Pages')
 <!-- Content Header (Page header) -->
<x-slot name="breadcrumb">
    <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Admin Informative Content Manager</h1>
        </div>
        <div class="col-sm-6">     
          {{ Breadcrumbs::render('common',['append' => [['label'=> 'Informative Content','route'=> 'admin.pages.index'],['label' => !empty($page) ? 'Edit Content' : 'Add Content' ]]]) }}
        </div>
      </div>
</x-slot>

<x-slot name="content">
  <!-- Main content -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Admin Informative Content</h3>

          <div class="card-tools">
          <a href="{{ route('admin.pages.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
        @if(isset($page))
                    {{ Form::model($page, ['route' => ['admin.pages.update', $page->id], 'method' => 'patch', 'id' => 'submit-page']) }}
                @else
                    {{ Form::open(['route' => ['admin.pages.store'], 'id' => 'submit-page']) }}
                @endif
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
                                <label for="title">Title</label>
                                {{ Form::text('title', old('title'), ['class' => 'form-control','placeholder' => 'Title']) }}
                                @if($errors->has('title'))
                                <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif
                              </div>


                              {{--<!-- <div class="form-group {{ $errors->has('sub_title') ? 'has-error' : '' }}">
                                <label for="title">Sub Title</label>
                                {{ Form::text('sub_title', old('sub_title'), ['class' => 'form-control','placeholder' => 'Sub Title']) }}
                                @if($errors->has('sub_title'))
                                <span class="help-block">{{ $errors->first('sub_title') }}</span>
                                @endif
                              </div>

                              <div class="form-group">
                                <label for="description">Short Description</label>
                                {{ Form::textarea('short_description', old('short_description'), ['class' => 'form-control','placeholder' => 'Short Description', 'rows' => 4]) }}
                            </div> -->--}}

                        </div>

                        <div class="col-md-6">

                            {{--<!-- <div class="form-group required {{ $errors->has('meta_title') ? 'has-error' : '' }}">
                                <label for="title">Meta Title</label>
                                {{ Form::text('meta_title', old('meta_title'), ['class' => 'form-control','placeholder' => 'Meta Title']) }}
                                @if($errors->has('meta_title'))
                                <span class="help-block">{{ $errors->first('meta_title') }}</span>
                                @endif
                              </div>

                              <div class="form-group required {{ $errors->has('meta_keyword') ? 'has-error' : '' }}">
                                <label for="title">Meta Keyword</label>
                                {{ Form::text('meta_keyword', old('meta_keyword'), ['class' => 'form-control','placeholder' => 'Meta Keyword']) }}
                                @if($errors->has('meta_keyword'))
                                <span class="help-block">{{ $errors->first('meta_keyword') }}</span>
                                @endif
                              </div>

                              <div class="form-group required {{ $errors->has('meta_description') ? 'has-error' : '' }}">
                                <label for="description">Meta Description</label>
                                {{ Form::textarea('meta_description', old('meta_description'), ['class' => 'form-control','placeholder' => 'Meta Description', 'rows' => 4]) }}
                                @if($errors->has('meta_description'))
                                <span class="help-block">{{ $errors->first('meta_description') }}</span>
                                @endif
                            </div> -->--}}

                        </div>


                    </div> <!-- /.row -->
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <div class="form-group required {{ $errors->has('description') ? 'has-error' : '' }}">
                                <label for="description">Description</label>
                                {{ Form::textarea('description', old('description'), ['class' => 'form-control ckeditor','placeholder' => 'Description', 'rows' => 8]) }}
                                @if($errors->has('description'))
                                <span class="help-block">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="chk-container">Is Active
                                @if(isset($page))
                                    @if($page->status == 1)
                                        <input name="status" type="checkbox" checked="checked">
                                    @else 
                                        <input name="status" type="checkbox">
                                    @endif
                                @else 
                                    <input name="status" type="checkbox" checked="checked">
                                @endif
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>


                </div><!-- /.box-body -->
                <div class="box-footer">
                        <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                    </div>
                    {{ Form::close() }}
        </div>
        <!-- /.card-body -->

      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.content -->
  @push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {   
            $("form").submit(function( event ) {
                $('.submit-form').attr('disabled', 'disabled');
            });
        });
    </script>  
    @endpush
</x-slot>
</x-layouts.admin>
