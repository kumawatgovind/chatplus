@php
$qparams = app('request')->query();
@endphp
<x-layouts.admin>
  @section('title', 'States')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>States Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'States', 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">States</h3>

            <div class="card-tools">
              <a href="{{ route('admin.states.create') }}" class="btn btn-primary btn-sm" title="Add State"><i class="fa fa-plus"></i> Add State</a>
              <a href="{{ route('admin.states.import') }}" class="btn btn-primary btn-sm" title="Add State Import"><i class="fa fa-plus"></i> Add State Import</a>
            </div>
          </div>
          <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                {{ Form::open(['url' => route('admin.states.index', $qparams),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Sate Name']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.states.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
                  </div>
                </div>
                {{ Form::close() }}
              </div>

            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="width:7%">#</th>
                  <th scope="col" class="actions">Country</th>
                  <th scope="col">@sortablelink('title', 'title', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
              </thead>
              @if($states->count() > 0)
              <tbody>
                @php
                $i = (($states->currentPage() - 1) * ($states->perPage()) + 1)
                @endphp
                @foreach($states as $state)

                <tr class="row-{{ $state->id }}">
                  <td> {{$i}}. </td>
                  <td>
                    @if($state->country)
                    {{ $state->country->name }}
                    @else
                    N/A
                    @endif
                  </td>
                  <td>{{$state->name}}</td>
                  <td>
                    @if ($state->status == 1)
                    <span class='btn btn-block btn-primary btn-xs updateStatus' data-value="1" data-column="status">Active</span>
                    @else
                    <span class='btn btn-block btn-danger btn-xs updateStatus' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $state->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.states.edit',[ $state->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit state" title="Edit state" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $state->name }}" title="Delete state" data-action="delete" data-message="Are you sure want to delete this state?" data-url="{{ route('admin.states.destroy', $state->id) }}" data-title="{{ $state->name }}"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                @php
                $i++;
                @endphp
                @endforeach
              </tbody>
              @else
              <tfoot>
                <tr>
                  <td colspan='7' align='center'> <strong>Record Not Available</strong> </td>
                </tr>
              </tfoot>
              @endif
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer clearfix">
            {{ $states->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>