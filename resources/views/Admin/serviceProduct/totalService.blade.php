@php
$title = "List Total Ad";
@endphp
<x-layouts.admin>
  @section('title', $title)
  <!-- Content Header (User header) -->
  <x-slot name="breadcrumb">
    <div class="row mb-2">
      <div class="col-sm-12">
        <h1>{{ $title }}</h1>
      </div>
      <div class="col-sm-12">
        {{ Breadcrumbs::render('common',['append' => [['label'=> $title, 'route'=> \Request::route()->getName()]]]) }}
      </div>
    </div>
  </x-slot>

  <x-slot name="content">
    <!-- Main content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
              </div>
              <div class="box-body">
                {{ Form::open(['url' => route('admin.getTotalService'),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder'
                    => 'Keyword e.g: Name, Email, Phone number']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i>
                      Filter</button>
                    <a href="{{ route('admin.getTotalService') }}" class="btn btn-warning" title="Reset"><i
                        class="fa  fa-refresh"></i> Reset</a>
                  </div>
                </div>
                {{ Form::close() }}
              </div>
            </div>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover">
              <thead>

                <tr>
                  <th style="width: 7%">#</th>
                  <th scope="col">User Name</th>
                  <th scope="col">@sortablelink('title', 'Ad Name', ['filter' => 'active, visible'], ['rel' =>
                    'nofollow'])</th>
                  <th scope="col">Price</th>
                  <th scope="col">Added On</th>
                  <th scope="col" width="10%">Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              @if($serviceProduct->count() > 0)
              <tbody>
                @php
                $i = (($serviceProduct->currentPage() - 1) * ($serviceProduct->perPage()) + 1)
                @endphp
                @foreach($serviceProduct as $service)
                <tr class="row-{{ $service->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$service->serviceUser->name}}</td>
                  <td>
                    @if($service->title)
                    {{$service->title}}
                    @else
                    {{$service->product_type.''.$service->product_for.''.$service->address}}
                    @endif
                  </td>
                  <td>{{ $service->price}}</td>
                  <td>{{ $service->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td>
                    @if($service->status == 1)
                    <span class="btn-block btn-success btn-xs text-center">Active</span>
                    @else
                    <span class="btn-block btn-danger btn-xs text-center">In-Active</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.serviceProductShow', $service->id) }}" class="btn btn-warning btn-xs"
                      data-toggle="tooltip" alt="View setting" title="View {{ $service->title }}"
                      data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                    <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip"
                      alt="Delete {{ $service->title }}" title="Delete {{ $service->title }}"
                      data-url="{{ route('admin.serviceProductDelete', $service->id) }}"
                      data-title="{{ $service->title }}"><i class="fa fa-trash"></i></a>
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
            {{ $serviceProduct->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>