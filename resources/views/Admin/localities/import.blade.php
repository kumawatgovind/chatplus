
<?php 
$title = "Locality Manager";
$add = "Import Localities";
?>
<x-layouts.admin>
  @section('title', $title)
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>{{$title}}</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $title,'route'=> 'admin.localities.index'],['label' => $add ]]]) }}
      </div>
    </div>
  </x-slot>
  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{$title}}</h3>
            <div class="card-tools">
              <a href="{{ route('admin.localities.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            {{ Form::open(['route' => ['admin.localities.import'], 'id' => 'submit-locality', 'files'=>'true']) }}
            <div class="box-body">
              <div class="row">
                <input type="hidden" id="cityStateUrl" value="{{ route('admin.cities.cityState') }}"/>
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('state_id') ? 'has-error' : '' }}">
                    <label for="state_id">States</label>
                    {{ Form::select('state_id', $states, old('state_id'), [
                      'class' => 'form-control',
                      'id' => 'localityStateId',
                      'placeholder' => 'Select state'
                      ]) }}
                    @if($errors->has('state_id'))
                    <span class="help-block">{{ $errors->first('state_id') }}</span>
                    @endif
                  </div>
                  <div class="form-group required {{ $errors->has('city_id') ? 'has-error' : '' }}">
                    <label for="city_id">City</label>
                    {{ Form::select('city_id', [], old('city_id'), [
                      'class' => 'form-control',
                      'id' => 'localityCityId',
                      'placeholder' => 'Select City'
                      ]) }}
                    @if($errors->has('city_id'))
                    <span class="help-block">{{ $errors->first('city_id') }}</span>
                    @endif
                  </div>
                  <div class="form-group {{ $errors->has('import') ? 'has-error' : '' }}">
                    <label for="importFile">Import File</label>
                    <div class="input-group">
                        <input type="file" class="form-control" name="import" id="importFile">
                    </div>
                    @if($errors->has('import'))
                    <span class="help-block">{{ $errors->first('import') }}</span>
                    @endif
                  </div>

                </div>
                <div class="col-md-6">
                </div>
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
              <a href="{{ route('admin.localities.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
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
  </x-slot>
</x-layouts.admin>