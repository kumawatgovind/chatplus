@php
$title = "Users Added Product";
@endphp
{{-- @dump($qparams) --}}
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
  <?php
  $response = Gate::inspect('check-user', "users-create");
  $canCreate = true;
  if (!$response->allowed()) {
    $canCreate = false;
  }
  ?>
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

                {{ Form::open(['url' => route('admin.getSavedProducts'),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Product Name']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.getSavedProducts') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">Product Name</th>
                  <th scope="col" width="15%">Product added On</th>
                  <th scope="col">User Name</th>
                  <th scope="col" width="15%">Member Since</th>
                </tr>
              </thead>
              @if($savedProducts->count() > 0)
              <tbody>
                @php
                $i = (($savedProducts->currentPage() - 1) * ($savedProducts->perPage()) + 1)
                @endphp
                @foreach($savedProducts as $product)
                <tr class="row-{{ $product->id }}">
                  <td> {{$i}}. </td>
                  <td>{{$product->title}}</td>
                  <td>{{ $product->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td>{{ $product->users->name}}</td>
                  <td>{{ $product->users->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
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
            {{ $savedProducts->appends(Request::query())->links() }}
          </div>
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>