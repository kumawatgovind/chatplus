@php
$qparams = app('request')->query();
@endphp
<x-layouts.admin>
  @section('title', 'Localities')
  <!-- Content Header (Service header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>Localities Manager</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> 'Localities', 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Localities</h3>

            <div class="card-tools">
              <a href="{{ route('admin.localities.create') }}" class="btn btn-primary btn-sm" title="Add Locality"><i class="fa fa-plus"></i> Add Locality</a>
              <a href="{{ route('admin.localities.import') }}" class="btn btn-primary btn-sm" title="Import Locality"><i class="fa fa-plus"></i> Import Localities</a>
            </div>
          </div>
          <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
                {{ Form::open(['url' => route('admin.localities.index', $qparams),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Locality Name']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.localities.index') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col" class="actions">City</th>
                  <th scope="col">@sortablelink('title', 'title', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th>@sortablelink('status', 'Status')</th>
                  <th scope="col">@sortablelink('created_at', 'Created', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" style="width:12%">Action</th>
                </tr>
              </thead>
              @if($localities->count() > 0)
              <tbody>
                @php
                $i = (($localities->currentPage() - 1) * ($localities->perPage()) + 1)
                @endphp
                @foreach($localities as $locality)

                <tr class="row-{{ $locality->id }}">
                  <td> {{$i}}. </td>
                  <td>
                    @if($locality->city)
                    {{ $locality->city->name }}
                    @else
                    N/A
                    @endif
                  </td>
                  <td>{{$locality->name}}</td>
                  <td>
                    @if ($locality->status == 1)
                    <span class='btn btn-block btn-primary btn-xs updateStatus' data-value="1" data-column="status">Active</span>
                    @else
                    <span class='btn btn-block btn-danger btn-xs updateStatus' data-value="0">In-Active</span>
                    @endif
                  </td>
                  <td>{{ $locality->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    <a href="{{ route('admin.localities.edit',[ $locality->id]) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit locality" title="Edit locality" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $locality->name }}" title="Delete locality" data-action="delete" data-message="Are you sure want to delete this locality?" data-url="{{ route('admin.localities.destroy', $locality->id) }}" data-title="{{ $locality->name }}"><i class="fa fa-trash"></i></a>
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
            {{ $localities->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>