<x-layouts.admin>
  @section('title', 'Tags')
  <!-- Content Header (Tags header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Admin Tags Manager</h1>
      </div>
      <div class="col-sm-6">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $getController,'route'=> 'admin.tags.index'],['label' => !empty($tag) ? 'Edit Tag' : 'Add Tag' ]]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Admin Tag</h3>
            <div class="card-tools">
              <a href="{{ route('admin.tags.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            @if(isset($tag))
            {{ Form::model($tag, ['route' => ['admin.tags.update', $tag->id], 'method' => 'patch', 'id' => 'submit-category', 'files'=>'true']) }}
            @else
            {{ Form::open(['route' => ['admin.tags.store'], 'id' => 'submit-tag', 'files'=>'true']) }}
            @endif
            <div class="box-body">
              <div class="row">

                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Name</label>
                    {{ Form::text('name', old('name'), ['class' => 'form-control','placeholder' => 'Name']) }}
                    @if($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                  </div>
                  
                </div>
                <div class="col-md-6" >
                  <div class="container">
                          
                  </div>
                </div>
              </div>
             
              <div class="row">
                <div class="col-md-12">
                  <label class="chk-container">Is Active
                    @if(isset($tag))
                    @if($tag->status == 1)
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
              <a href="{{ route('admin.tags.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
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
    @endpush
    @push('scripts')    
    @endpush

  </x-slot>
</x-layouts.admin>