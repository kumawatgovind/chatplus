@php
$title = "Service Product";
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
        {{ Breadcrumbs::render('common',['append' => [['label'=> $title,'url'=> url()->previous()],['label' => 'View '.$title.' Detail']]]) }}


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

            <div class="card-tools">
              <a href="{{ route('admin.getTotalService') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-hover table-striped">
              <tr>
                <th scope="row">{{ __('User Name') }}</th>
                <td>{{ $user->name }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('User Email') }}</th>
                <td>{{ $user->email }}</td>
              </tr>

              <tr>
                <th scope="row">{{ __('User Phone') }}</th>
                <td>{{ $user->country_code.$user->phone_number }}</td>
              </tr>

              <tr>
                <th scope="row">{{ __('Product Name') }}</th>
                <td>{{ $user->title }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Product Price') }}</th>
                <td>{{ $user->price }}</td>
              </tr>
              @if($user->catName)
              <tr>
                <th scope="row">{{ __('Product Category Name') }}</th>
                <td>{{ $user->catName }}</td>
              </tr>
              @endif
              @if($user->subCatName)
              <tr>
                <th scope="row">{{ __('Product Sub Category Name') }}</th>
                <td>{{ $user->subCatName }}</td>
              </tr>
              @endif
              <tr>
                <th scope="row">{{ __('Product Type') }}</th>
                <td>{{ $user->product_type }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Product For') }}</th>
                <td>{{ $user->product_for }}</td>
              </tr>
              
              @if ($user->description)
              <tr>
                <th scope="row">{{ __('Description') }}</th>
                <td>{{ $user->description == null ? 'NA' : $user->description }}</td>
              </tr>
              @endif

              <tr>
                <th scope="row">{{ __('Status') }}</th>
                <td>
                  @if ($user->product_status == 1)
                  <span class='btn1 btn-block btn-success btn-xs updateStatus text-center' style="width: 10%;" data-value="1" data-column="status">Active</span>
                  @else
                  <span class='btn1 btn-block btn-danger btn-xs updateStatus text-center' style="width: 10%;" data-value="0">In-Active</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td>{{ $user->service_added ? date(config('get.ADMIN_DATE_FORMAT'), strtotime($user->service_added)) : 'Not Available' }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Modified') }}</th>
                <td>{{ $user->service_updated ? date(config('get.ADMIN_DATE_FORMAT'), strtotime($user->service_updated)) : 'Not Available' }}</td>
              </tr>

            </table>
          </div>

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.content -->
  </x-slot>
</x-layouts.admin>