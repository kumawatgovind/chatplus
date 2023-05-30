<x-layouts.admin>
@section('title', !empty($emailHook) ? 'Edit Email Hook' : 'Add Email Hook')
@section('content')
@include('layouts.admin.flash.alert')
<section class="content-header">
    <h1>
        Manage Email Hooks
        <small>Here you can {{ !empty($emailHook) ? 'edit' : 'add' }} email hook(slug)</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route("admin.dashboard") }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{route('admin.hooks')}}">{{ __("Hooks") }}</a></li>
        <li><a href="javascript:void(0)" class="active">{{ !empty($emailHook) ? 'Edit Email Hook' : 'Add Email Hook' }}</a></li>
    </ol>
</section>
<section class="content" data-table="emailHooks">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-info emailHooks">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ !empty($emailHook) ? 'Edit Email Hook' : 'Add Email Hook' }} </h3>
                    <a href="{{ route('admin.hooks') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
                </div><!-- /.box-header -->
                <form method="POST" action="{{ route('admin.save-hooks',['id' => !empty($emailHook) ? $emailHook)->id : null]) }}">
                        @csrf

                        @if (!empty($emailHook))
                            @method('PUT')
                        @endif

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group required">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" value="{{ old("title") ? old("title") : (!empty($emailHook) ? $emailHook->title : '') }}" placeholder="Title" required="required" maxlength="150" id="title">
                            </div>     
                            <div class="form-group">
                                <label for="slug">Hook</label>
                                <input type="text" name="slug" class="form-control" value="{{ old("slug") ? old("slug") : (!empty($emailHook) ? $emailHook->slug : '') }}" placeholder="Hook" maxlength="150" id="slug">                        
                                 <p class="help-block">No space, separate each word with underscore. (if you want auto generated then please leave blank)</p>
                            </div>     
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" placeholder="Description" required="required" id="description" rows="5">{{ old("description") ? old("description") : (!empty($emailHook) ? $emailHook->description : '') }}</textarea>
                            </div>     
                            <div class="form-group">
                                <label for="status">Status</label> 
                                <select name="status" class="form-control" id="status">
                                    <option {{ (old("status") ? old("status") : (!empty($emailHook) ? $emailHook->description : null))  === 1 ? "selected='selected'":"" }} value="1">Active</option>
                                    <option {{ (old("status") ? old("status") : (!empty($emailHook) ? $emailHook->description : null))  === 0 ? "selected='selected'":"" }} value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer">
                        <button class="btn btn-primary btn-flat" title="Submit" type="submit"><i class="fa fa-fw fa-save"></i> Submit</button>  
                        <a href="{{ route('admin.hooks') }}" class="btn btn-warning btn-flat" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>                
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-anchor"></i> Last updated email hooks</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- form start -->
                    
                </div>

            </div>
            <!-- /.box -->
        </div>
    </div>
</section>
@endsection
</x-layouts.admin>