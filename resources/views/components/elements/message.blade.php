@if(session()->has('success'))
<div class="alert alert-success fade in alert-dismissible show">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button> <strong>Success!</strong> {{ session()->get('success') }}
</div>
@endif
@if(session()->has('error'))
<div class="alert alert-danger fade in alert-dismissible show">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button> <strong>Error!</strong> {{ session()->get('error') }}
</div>
@endif

@if (session()->has('ValidatorError'))
<div class="alert alert-danger fade in alert-dismissible show">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button> <strong>Error!</strong> {{ session()->get('ValidatorError') }}
</div>
@endif

@if (session()->has('warning'))

<div class="alert alert-warning fade in alert-dismissible show">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button> <strong>Warning!</strong> {{ session()->get('warning') }}
</div>

@endif


@if (session()->has('info'))

<div class="alert alert-info fade in alert-dismissible show">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">×</span>
  </button> <strong>Info!</strong> {{ session()->get('info') }}
</div>

@endif