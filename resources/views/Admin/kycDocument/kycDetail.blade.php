
<?php 
$title = "Kyc Manager";
$add = "Kyc Detail";
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
        {{ Breadcrumbs::render('common',['append' => [['label'=> $title,'route'=> 'admin.getPendingKyc'],['label' => 'User Kyc' ]]]) }}
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
              <a href="{{ route('admin.getPendingKyc') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            {{ Form::open(['route' => ['admin.updateKyc'], 'id' => 'submit-kyc', 'files'=>'true']) }}
            <input type="hidden" name="kyc_id" value="{{ $kycDetail->id }}" />
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="is_kyc">User Name</label>
                    <div class="form-control">
                      {{ $kycDetail->user->name }}
                    </div>
                  </div>
                  @if($kycDetail->user->email)
                  <div class="form-group">
                    <label for="is_kyc">User Email</label>
                    <div class="form-control">
                      {{ $kycDetail->user->email }}
                    </div>
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="is_kyc">User Phone Number</label>
                    <div class="form-control">
                      {{ $kycDetail->user->country_code }}{{ $kycDetail->user->phone_number }}
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="is_kyc">User Aadhar Number</label>
                    <div class="form-control">
                      {{ $kycDetail->aadhar_number }}
                    </div>
                  </div>
                  @if($kycDetail->pan_number)
                  <div class="form-group">
                    <label for="is_kyc">User Pan Number</label>
                    <div class="form-control">
                      {{ $kycDetail->pan_number }}
                    </div>
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="is_kyc">User A/C Holder Name</label>
                    <div class="form-control">
                      {{ $kycDetail->account_holder_name }}
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="is_kyc">User Bank Name</label>
                    <div class="form-control">
                      {{ $kycDetail->bank_name }}
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="is_kyc">User A/C Number</label>
                    <div class="form-control">
                      {{ $kycDetail->bank_account_number }}
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="is_kyc">User IFSC Code</label>
                    <div class="form-control">
                      {{ $kycDetail->bank_ifsc_code }}
                    </div>
                  </div>
                  
                </div>
                <div class="col-md-6 kycImages">
                  <div class="form-group">
                    <label for="is_kyc">Aadhar Front</label>
                    <div class="form-control">
                      <img src="{{ $kycDetail->document_base_url.'/'.$kycDetail->aadhar_front }}" alt="" style="width:100%">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="is_kyc">Aadhar Back</label>
                    <div class="form-control">
                      <img src="{{ $kycDetail->document_base_url.'/'.$kycDetail->aadhar_back }}" alt="" style="width:100%">
                    </div>
                  </div>
                  @if($kycDetail->pan_front)
                  <div class="form-group">
                    <label for="is_kyc">Pan Card</label>
                    <div class="form-control">
                      <img src="{{ $kycDetail->document_base_url.'/'.$kycDetail->pan_front }}" alt="" style="width:100%">
                    </div>
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="is_kyc">Bank Passbook</label>
                    <div class="form-control">
                      <img src="{{ $kycDetail->document_base_url.'/'.$kycDetail->passbook_image }}" alt="" style="width:100%">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group required {{ $errors->has('is_kyc') ? 'has-error' : '' }}">
                    @php
                    $isKyc = 0;
                    if ($kycDetail->is_kyc) {
                      $isKyc = $kycDetail->is_kyc;
                    }
                    @endphp
                    <label for="is_kyc">Kyc Status</label>
                    {{ Form::select('is_kyc', $kycStatus, $isKyc, ['class' => 'form-control','placeholder' => 'Select kyc status']) }}
                    @if($errors->has('is_kyc'))
                    <span class="help-block">{{ $errors->first('is_kyc') }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
              <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>
              <a href="{{ route('admin.getPendingKyc') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
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
  </x-slot>
</x-layouts.admin>