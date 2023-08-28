<x-layouts.master>
@section('title', !empty($enquiry) ? 'Reply Contact' : 'Add Contact')
    <!-- Content Header (Contact header) -->
    <x-slot name="breadcrumb">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Admin Enquiry Manager</h1>
        </div>
        <div class="col-sm-6">
        {{ Breadcrumbs::render('common', ['append' => [['label'=> 'Enquiries','route'=> 'admin.contacts.index'],['label' => 'Reply Enquiry Detail']]]) }}
        </div>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="row enquiries">
            <div class="col-md-12">
                <div class="card box-info">
                    <div class="card-header with-border">
                        <h3 class="card-title">{{ !empty($enquiry) ? 'Reply to '.$enquiry->name : 'Add Contact' }} </h3>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-default float-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                    </div><!-- /.card-header -->
                    @if(isset($enquiry))
                        {{ Form::model($enquiry, ['route' => ['admin.contacts.contactUpdate', $enquiry->id], 'method' => 'patch', 'id' => 'submit-enquiry']) }}
                    @else
                        {{ Form::open(['route' => ['admin.contacts.store'], 'id' => 'submit-enquiry']) }}
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><label>Posted Contact enquiry : </label>{!! $enquiry->message !!}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group required {{ $errors->has('reply_text') ? 'has-error' : '' }}">
                                    <label for="reply_text">Reply</label>
                                    {{ Form::textarea('reply_text', old('reply_text'), ['class' => 'form-control','placeholder' => 'Reply to user (User will receive over an email)', 'rows' => 4]) }}
                                    @if($errors->has('reply_text'))
                                    <span class="help-block">{{ $errors->first('reply_text') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div><!-- /.card-body -->
                    <div class="card-footer">
                        <button class="btn btn-primary btn-flat submit-form" title="Submit" type="submit">
                            <i class="fa fa-fw fa-save"></i> Submit
                        </button>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @push('scripts')
        <script type="text/javascript">
            $(document).ready(function () {   
                $("#submit-enquiry").submit(function( event ) {
                    $('.submit-form').attr('disabled', 'disabled');
                });
            });
        </script>  
        @endpush
    </x-slot>
</x-layouts.master>