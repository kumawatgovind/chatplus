@php
$title = "Users";
$add = "User";
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
        {{ Breadcrumbs::render('common',['append' => [['label'=> $title,'url'=> url()->previous()],['label' => $add.'Detail']]]) }}
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
              <a href="{{ route('admin.users.index') }}" class="btn btn-default pull-right" title="Cancel"><i class="fa fa-fw fa-chevron-circle-left"></i> Back</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-hover table-striped">
              <tr>
                <th scope="row">{{ __('Name') }}</th>
                <td>{{ $user->name }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Email') }}</th>
                <td>{{ $user->email }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Phone Number') }}</th>
                <td>{{$user->country_code }}{{ $user->phone_number}}</td>
              </tr>
              @if ($user->reportedUser)
              <tr>
                <th scope="row">{{ __('Reported Reason') }}</th>
                <td>{{$user->reportedUser->description}}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Reported By') }}</th>
                <td>{{$user->reportedUser->userByReport->name}}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Reported On') }}</th>
                <td>{{$user->reportedUser->created_at->format(config('get.ADMIN_DATE_FORMAT'))}}</td>
              </tr>
              @endif
              <tr>
                <th scope="row">{{ __('Status') }}</th>
                <td>
                  @if ($user->status == 1)
                  <span class='btn1 btn-block btn-success btn-xs updateStatus text-center' style="width: 10%;" data-value="1" data-column="status">Active</span>
                  @else
                  <span class='btn1 btn-block btn-danger btn-xs updateStatus text-center' style="width: 10%;" data-value="0">In-Active</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th scope="row"><?= __('Created') ?></th>
                <td>{{ $user->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
              </tr>
              <tr>
                <th scope="row">{{ __('Modified') }}</th>
                <td>{{ $user->updated_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
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