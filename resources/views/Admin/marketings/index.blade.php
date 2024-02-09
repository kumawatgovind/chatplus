@php
$qparams = app('request')->query();
@endphp
<x-layouts.admin>
  @section('title', 'Marketing Banner')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Marketing Banner Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Marketing Banner Manager', 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Marketing Banner</h3>
            <div class="card-tools">
              <a href="{{ route('admin.marketings.create') }}" class="btn btn-primary btn-sm" title="Add Marketing Banner"><i class="fa fa-plus"></i> Add Marketing Banner</a>
            </div>
          </div>
          {{--<!-- <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                {{ Form::open(['url' => route('admin.marketings.index', $qparams),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-3 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Service Name']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.marketings.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
                  </div>
                </div>
                {{ Form::close() }}
              </div>
            </div>
          </div>  -->--}}
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="width:7%">#</th>
                  <th scope="col">@sortablelink('title', 'Banner Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
              </thead>
              @if($marketings->count() > 0)
              <tbody>
                @php
                $i = (($marketings->currentPage() - 1) * ($marketings->perPage()) + 1)
                @endphp
                @foreach($marketings as $marketing)

                <tr class="row-{{ $marketing->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$marketing->name}}</td>
                  <td>
                    @if ($marketing->status == 1)
                    <span class='btn btn-block btn-primary btn-xs updateStatus' data-value="1" data-column="status">Active</span>
                    @else
                    <span class='btn btn-block btn-danger btn-xs updateStatus' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $marketing->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <!-- <a href="{{ route('admin.marketings.show', [$marketing->id]) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View marketing banner" title="View marketing banner" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a> -->
                    <a href="{{ route('admin.marketings.edit', [$marketing->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit marketing banner" title="Edit marketing banner" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $marketing->name }}" title="Delete {{ $marketing->name }}" data-action="delete" data-message="Are you sure want to delete this marketing?" data-url="{{ route('admin.marketings.destroy', $marketing->id) }}" data-title="{{ $marketing->name }}"><i class="fa fa-trash"></i></a>
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
            {{ $marketings->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>