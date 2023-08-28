@php
$currentRoute = Route::currentRouteName();
if ($currentRoute == "admin.runningListing") {
$title = "Running Business Listing";
} elseif($currentRoute == "admin.blockedSpam") {
$title = "Blocked/Spam Business Listing";
} else {
$title = "Business Listing";
}
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
                {{ Form::open(['url' => route('admin.businessListing'),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Business Name, Contact Person, Phone number']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.businessListing') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">@sortablelink('name', 'Business Name', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">Contact Person</th>
                  <th scope="col">Phone Number</th>
                  <th scope="col">ChatPlus Account</th>
                  <th scope="col">Business added Date</th>
                </tr>
              </thead>
              @if($serviceProfile->count() > 0)
              <tbody>
                @php
                $i = (($serviceProfile->currentPage() - 1) * ($serviceProfile->perPage()) + 1)
                @endphp
                @foreach($serviceProfile as $business)
                <tr class="row-{{ $business->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$business->service_name}}</td>
                  <td>{{ $business->contact_person }}</td>
                  <td>{{ $business->mobile_number }}</td>
                  <td>{{ $business->user->name }}</td>
                  <td>{{ $business->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
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
            {{ $serviceProfile->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>