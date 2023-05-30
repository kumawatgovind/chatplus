<x-layouts.admin>
  @section('title', 'Post')
  <!-- Content Header (Gallery header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Admin Post Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.posts.index'],['label' => !empty($post) ? 'Edit Post' : 'Add Post' ]]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Post</h3>
            <div class="card-tools">
              <a href="{{ route('admin.posts.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            @if(isset($post))
            {{ Form::model($post, ['route' => ['admin.posts.update', $post->id], 'method' => 'patch', 'id' => 'submit-location']) }}
            {{ Form::hidden('user_id', $post->user_id) }}
            @else
            {{ Form::open(['route' => ['admin.posts.store'], 'id' => 'submit-location']) }}
            {{ Form::hidden('user_id', app('request')->query('u')) }}
            @endif
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('category_id') ? 'has-error' : '' }}">
                    <label for="category_id">Category</label>
                    {{ Form::select('category_id', $catData, old('category_id'), ['class' => 'form-control','placeholder' => 'Select Category']) }}
                    @if($errors->has('category_id'))
                    <span class="help-block">{{ $errors->first('category_id') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('type') ? 'has-error' : '' }}">
                    <label for="postType">Post Type</label>
                    {{ Form::select('type', config('constants.POST_TYPE'), old('type'), ['id' => 'postType', 'class' => 'form-control','placeholder' => 'Select Post Type']) }}
                    @if($errors->has('type'))
                    <span class="help-block">{{ $errors->first('type') }}</span>
                    @endif
                  </div>
                </div>
              </div> <!-- /.row -->
              <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('post_visibility') ? 'has-error' : '' }}">
                    <label for="postVisibility">Post visibility</label>
                    {{ Form::select('post_visibility', config('constants.POST_VISIBILITY'), old('post_visibility'), ['id' => 'postVisibility', 'class' => 'form-control','placeholder' => 'Select Post Visibility']) }}
                    @if($errors->has('post_visibility'))
                    <span class="help-block">{{ $errors->first('post_visibility') }}</span>
                    @endif
                  </div>
                </div>
              </div> <!-- /.row -->
              <div class="row">
                <div class="col-md-6">
                  @if(!empty($mentionedUsers))
                  <div class="form-group">
                    <label for="mentionedUsers">Mentioned Users</label>
                    <select class="js-example-basic-multiple form-control" id="mentionedUsers" name="mentioned_users[]" multiple="multiple">
                      @foreach($mentionedUsers as $userId => $user)
                      <option value="{{ $userId }}">{{ $user }}-{{ $userId }}</option>
                      @endforeach
                    </select>
                  </div>
                  @endif
                </div>
                <div class="col-md-6">
                @if(!empty($mentionedTags))
                  <div class="form-group">
                    <label for="mentionedTags">Mentioned Tags</label>
                    <select class="js-example-basic-multiple form-control" id="mentionedTags" name="mentioned_tags[]" multiple="multiple">
                      @foreach($mentionedTags as $id => $name)
                      <option value="{{ $id }}">{{ $name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
                </div>
              </div> <!-- /.row -->
              <div class="row">
                <div class="col-sm-12">
                    <table id="bannerImages" style="display: none;" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-left" style="width: 40%;">Title</th>
                                <th class="text-right" style="width: 40%;">Image</th>
                                <th  style="width: 8%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $key = 0; @endphp
                        @if(!empty($post))
                            @foreach($post->attachments as $key => $media_file)
                            <tr id="imageBox-{{ $key }}" class="imageBox">
                                <td class="text-left">
                                    <div class="form-group required {{ $errors->has('attachments.'.$key.'.title') ? 'has-error' : '' }}">
                                        <input type="text" name="attachments[{{ $key }}][title]" placeholder="Title" class="form-control" value="{{ $media_file->title }}" />
                                        @if($errors->has('attachments.'.$key.'.title'))
                                        <span class="help-block">The title is required.</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="thumbimage" id="imageBox-{{ $key }}">
                                    @if(!empty($media_file->image_path) || file_exists($media_file->image_path))
                                        <img src="{{ url($media_file->image_path) }}" class="img-thumbnail" data-placeholder="{{ url($media_file->image_path) }}" style="max-height:150px;" alt="">

                                    @else
                                    <img src="{{ url('img/no-icon.jpg') }}" class="img-thumbnail" data-placeholder="{{ url('img/no-icon.jpg') }}" style="max-height:150px;" alt="">

                                    @endif
                                    </div>
                                    <button class="btn bg-olive btn-flat margin button-upload" data-toggle="tooltip" title="" data-loading-text="Loading..." type="button" data-original-title="Upload File"><i class="fa fa-upload"></i>Upload</button>
                                    <input name="attachments[{{ $key }}][image_path]" class="input-image" id="{{ $key }}-config-value" value="{{ $media_file->image_path }}" type="hidden">
                                    <input name="attachments[{{ $key }}][name]" class="input-image-name" id="{{ $key }}-config-value" value="{{ $media_file->name }}" type="hidden">
                                    <div class="form-group required {{ $errors->has('attachments.'.$key.'.image_path') ? 'has-error' : '' }}">
                                        @if($errors->has('attachments.'.$key.'.image_path'))
                                        <span class="help-block">The file is required.</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-left">
                                    <button type="button" onclick="$('#imageBox-{{ $key }}, .tooltip').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @elseif(!empty(old('attachments')))
                            @foreach(old('attachments')  as $key => $media_file)
                            @php $media_file = (object) $media_file @endphp
                            <tr id="imageBox-{{ $key }}" class="imageBox">
                                <td class="text-left">
                                    <div class="form-group required {{ $errors->has('attachments.'.$key.'.title') ? 'has-error' : '' }}">
                                        <input type="text" name="attachments[{{ $key }}][title]" placeholder="Title" class="form-control" value="{{ $media_file->title??'' }}" />
                                        @if($errors->has('attachments.'.$key.'.title'))
                                        <span class="help-block">The title is required.</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="thumbimage" id="imageBox-{{ $key }}">
                                    @if(!empty($media_file->image_path) || file_exists($media_file->image_path))
                                      <img src="{{ url($media_file->image_path) }}" class="img-thumbnail" data-placeholder="{{ url($media_file->image_path) }}" style="max-height:150px;" alt="">


                                    @else
                                        <img src="{{ url('img/no-icon.jpg') }}" class="img-thumbnail" data-placeholder="{{ url('img/no-icon.jpg') }}" style="max-height:150px;" alt="">
                                    @endif
                                    </div>
                                    <button class="btn bg-olive btn-flat margin button-upload" data-toggle="tooltip" title="" data-loading-text="Loading..." type="button" data-original-title="Upload File"><i class="fa fa-upload"></i>Upload</button>
                                    <input name="attachments[{{ $key }}][image_path]" class="input-image" id="{{ $key }}-config-value" value="{{ $media_file->image_path }}" type="hidden">
                                    <input name="attachments[{{ $key }}][name]" class="input-image-name" id="{{ $key }}-config-value" value="{{ $media_file->name }}" type="hidden">
                                    <div class="form-group required {{ $errors->has('attachments.'.$key.'.image') ? 'has-error' : '' }}">
                                        @if($errors->has('attachments.'.$key.'.image_path'))
                                        <span class="help-block">The file is required.</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-left">
                                    <button type="button" onclick="$(\'#imageBox-{{ $key }}, .tooltip\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-left">
                                    <button type="button" onclick="addImage();" data-toggle="tooltip" title="<?= __('Add Banner') ?>" class="btn btn-primary">
                                        <i class="fa fa-plus-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="form-group" id="bannerVideos" style="display: none;">
                      <button class="btn bg-olive btn-flat margin button-upload" data-toggle="tooltip" title="" data-loading-text="Loading..." type="button" data-original-title="Upload File"><i class="fa fa-upload"></i>Upload</button>
                      <input name="attachments[0][image_path]" class="input-video" id="0-config-value" value="{{-- $media_file->image_path --}}" type="hidden">
                      <input name="attachments[0][name]" class="input-video-name" id="0-config-value" value="{{-- $media_file->name --}}" type="hidden"> 
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label class="chk-container">Is Active
                    @if(isset($post))
                    @if($post->status == 1)
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
              <div class="row" style="margin-top: 15px;">
                <div class="col-md-12">

                  <div class="form-group  {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">Description</label>
                    {{ Form::textarea('description', old('description'), ['class' => 'form-control ckeditor','placeholder' => 'Description', 'rows' => 8]) }}
                    @if($errors->has('description'))
                    <span class="help-block">{{ $errors->first('description') }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              <button id="postSubmit" class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
              <a href="{{ route('admin.posts.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
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
  </x-slot>
@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endpush
@push('scripts')

<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2({
      placeholder: 'Select an option'
    });
  });
    $(document).ready(function () {
        
        $('#postType').on('change', function(){
          var _this = $(this);
          if(_this.val() == 1) {
            $("#bannerImages").show();
            $("#bannerVideos").hide();
          } else if (_this.val() == 2) {
            $("#bannerVideos").show();
            $("#bannerImages").hide();
          } else {
            $("#bannerVideos").hide();
            $("#bannerImages").hide();

          }
        })
    });
  var image_row = <?= ($key+1)?>;

      function addImage()
      {
        
        var count_elements =$('tr.imageBox').length;
        if (count_elements < 5) {
          html = '<tr id="imageBox-' + image_row + '" class="imageBox">';
          html += '  <td class="text-left"><input type="text" name="attachments[' + image_row + '][title]" placeholder="Title" class="form-control" /></td>';

          html += '  <td>';
          html += '<div class="thumbimage" id="imageBox-' + image_row + '">';
          html += ' <img src="{{ url('img/no-icon.jpg') }}" class="img-thumbnail" data-placeholder="{{ url('img/no-icon.jpg') }}" style="max-height:150px;" alt="">';
          html += '</div>';
          html += '<button class="btn bg-olive btn-flat margin button-upload" data-toggle="tooltip" title="" data-loading-text="Loading..." type="button" data-original-title="Upload File"><i class="fa fa-upload"></i>Upload</button>';
          html += '<input name="attachments[' + image_row + '][image_path]" class="input-image" id="' + image_row + '-config-value" value="" type="hidden">';
          html += '<input name="attachments[' + image_row + '][name]" class="input-image-name" id="' + image_row + '-config-value" value="" type="hidden">';
          html += '  </td>';
          html += '  <td class="text-left"><button type="button" onclick="$(\'#imageBox-' + image_row + ', .tooltip\').remove();" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
          html += '</tr>';
          $('table#bannerImages tbody').append(html);
        }
      image_row++;
      }
      $(document).on('click','.button-upload', function () {
          var _this = $(this);
          if ($('#postType').val() == 2) {
            var inputValue = $("#bannerVideos").find("input.input-video");
            var inputNameValue = $("#bannerVideos").find("input.input-video-name");
          } else {
            var inputValue = _this.closest("tr").find("input.input-image");
            var inputNameValue = _this.closest("tr").find("input.input-image-name");
            var iconBox = _this.closest("tr").find("img");
          }
          var postType = $('#postType').val();
          $('#form-upload').remove();
          var fields = '<input type="file" name="file" />' + '<input type="hidden" name="postType" value="'+postType+'" />';
          $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;">' + fields + '</form>');
          $('#form-upload input[name=\'file\']').trigger('click');
          if (typeof timer != 'undefined') {
              clearInterval(timer);
          }
          timer = setInterval(function () {
            if ($('#form-upload input[name=\'file\']').val() != '') {
                clearInterval(timer);
                $.ajax({
                    url: '{{ route("admin.posts.uploadImage") }}',
                    type: 'post',
                    dataType: 'json',
                    data: new FormData($('#form-upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function (xhr) {
                        $("#postSubmit[type='submit']").prop("disabled", true);
                        _this.closest("tr").find(".button-upload").button('loading..');
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    },
                    complete: function () {
                        _this.closest("tr").find(".button-upload").button('reset');
                    },
                    success: function (json) {
                      $("#postSubmit[type='submit']").prop("disabled", false);
                        if (json.success === true) {
                            inputValue.val(json.image_path);
                            inputNameValue.val(json.filename);
                            iconBox.attr('src', json.image_path);
                            wowMsg(json.message);
                        } else {
                            inputValue.val('');
                            if (postType == 1) {
                              iconBox.attr('src', "{{ url('img/no-icon.jpg') }}");
                            }
                            $.alert({
                                columnClass: 'medium',
                                title: 'Error',
                                icon:  'fa fa-warning',
                                type:  'red',
                                content: json.message,
                            });
                        }

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
          }, 500);
      });

      

  </script>
@endpush
</x-layouts.admin>