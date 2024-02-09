<x-layouts.admin>
  @section('title', 'Marketing Banner')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Admin Marketing Banner Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Marketing Banner Manager','route'=> 'admin.marketings.index'],['label' => !empty($marketing) ? 'Edit Marketing Banner' : 'Add Marketing Banner' ]]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Marketing Banner</h3>
            <div class="card-tools">
              <a href="{{ route('admin.marketings.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            @if(isset($marketing))
            {{ Form::model($marketing, ['route' => ['admin.marketings.update', $marketing->id], 'method' => 'patch', 'id' => 'submit-marketing', 'files'=>'true']) }}
            @else
            {{ Form::open(['route' => ['admin.marketings.store'], 'id' => 'submit-marketing', 'files'=>'true']) }}
            @endif
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('type') ? 'has-error' : '' }}">
                    <label for="type">Marketing Media type</label>
                    {{ Form::select('type', [1 => 'Image',2 => 'Video'], old('type'),['class' => 'form-control','placeholder' => 'Select Media type']) }}
                    @if($errors->has('type'))
                    <span class="help-block">{{ $errors->first('type') }}</span>
                    @endif
                  </div>
                  <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Marketing name</label>
                    {{ Form::text('name', old('name'),['class' => 'form-control','placeholder' => 'Enter media name']) }}
                    @if($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6 kycImages">
                  <div class="form-group required {{ $errors->has('url_link') ? 'has-error' : '' }}">
                    <label for="url_link">Marketing media Link</label>
                    {{ Form::text('url_link', old('url_link'),['class' => 'form-control','placeholder' => 'Enter youtube link']) }}
                    @if($errors->has('url_link'))
                    <span class="help-block">{{ $errors->first('url_link') }}</span>
                    @endif
                  </div>
                  <div class="form-group {{ $errors->has('media_name') ? 'has-error' : '' }}">
                    <label for="mediaName">Marketing Banner</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="media_name" id="mediaName">
                    </div>
                    @if($errors->has('media_name'))
                    <span class="help-block">{{ $errors->first('media_name') }}</span>
                    @endif
                  </div>
				          @if(isset($marketing))
                  <div class="form-group">
                    <div class="form-control">
                    <img src="{{ $marketing->media_url }}" alt="" style="width:100%">
                    </div>
                  </div>
				          @endif
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <label class="chk-container">Is Active
                    @if(isset($marketing))
                    @if($marketing->status == 1)
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
              <a href="{{ route('admin.marketings.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
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

    <!-- /.content -->
    @push('styles')
    <style>
       .kycImages .form-group .form-control {
          height: auto;
        }
      
    </style>

    @endpush
    @push('scripts')
    
    @endpush

  </x-slot>
</x-layouts.admin>