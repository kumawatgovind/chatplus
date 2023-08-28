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
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Name, Email, Phone number']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.getTotalService') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">@sortablelink('title', 'Ad Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">Price</th>
                  <th scope="col">Added On</th>
                  <th scope="col" width="10%">Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              @if($users->count() > 0)
              <tbody>
                @php
                $i = (($users->currentPage() - 1) * ($users->perPage()) + 1)
                @endphp
                @foreach($users as $user)
                <tr class="row-{{ $user->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$user->name}}</td>
                  <td>{{$user->title}}</td>
                  <td>{{ $user->price}}</td>
                  <td>{{ $user->service_added ? date(config('get.ADMIN_DATE_FORMAT'), strtotime($user->service_added)) : 'Not Available' }}</td>
                  <td>
                    @if($user->product_status == 1)
                    <span class="btn-block btn-success btn-xs text-center">Active</span>
                    @else
                    <span class="btn-block btn-danger btn-xs text-center">In-Active</span>
                    @endif
                  </td>
                  <td>
                  <a href="{{ route('admin.serviceProductShow', $user->service_product_id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" alt="View setting" title="View {{ $user->title }}" data-original-title="View"><i class="fa fa-fw fa-eye"></i></a>
                  <a href="javascript:void(0);" class="confirmDeleteBtn btn btn-danger btn-xs" data-toggle="tooltip" alt="Delete {{ $user->title }}" title="Delete {{ $user->title }}" data-url="{{ route('admin.serviceProductDelete', $user->service_product_id) }}" data-title="{{ $user->title }}"><i class="fa fa-trash"></i></a>
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
            {{ $users->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>