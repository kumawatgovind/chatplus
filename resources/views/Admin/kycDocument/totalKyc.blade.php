@php
$title = "List total Kyc users";
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
                {{ Form::open(['url' => route('admin.getTotalKyc'),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Name, Email, Phone number']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.getTotalKyc') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">@sortablelink('name', 'Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">Phone Number</th>
                  <th scope="col">Member Since</th>
                  <th scope="col" width="10%">Status</th>
                  <th scope="col">Kyc Date</th>
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
                  <td>{{ $user->country_code.$user->phone_number}}</td>
                  <td>{{ $user->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td>
                    @if($user->is_kyc == 1)
                    <span class="btn-block btn-success btn-xs text-center">{{ $kycStatus[$user->is_kyc] }}</span>
                    @elseif($user->is_kyc == 2)
                    <span class="btn-block btn-warning btn-xs text-center">{{ $kycStatus[$user->is_kyc] }}</span>
                    @else
                    <span class="btn-block btn-danger btn-xs text-center">{{ $kycStatus[$user->is_kyc] }}</span>
                    @endif
                  </td>
                  <td>{{ $user->kyc_done ? date(config('get.ADMIN_DATE_FORMAT'), strtotime($user->kyc_done)) : 'Not Available' }}</td>
                  <td>
                  @if(!empty($user->kyc_document_id) && $user->is_kyc != 1)
                  <a href="{{ route('admin.getSingleKyc',$user->kyc_document_id) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" alt="Edit Kyc" title="Edit Kyc" data-original-title="Edit"><i class="fa fa-edit"></i></a>
                  @endif
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