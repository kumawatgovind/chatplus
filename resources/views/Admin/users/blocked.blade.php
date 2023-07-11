@php
$title = "Blocked Users";
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
            @if($canCreate)
            <div class="card-tools">
              {{--
              <a href="{{ route('admin.users.create') }}" class="btn btn-block btn-primary btn-sm" title="Add {{ $add }}"><i class="fa fa-plus"></i> Add {{ $add }}</a>
              --}}
            </div>
            @endif
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="filter-outer box box-info">
              <div class="box-header">
                <h3 class="box-title"><span class="caption-subject font-green bold uppercase">Filter</span></h3>
              </div>
              <div class="box-body">

                {{ Form::open(['url' => route('admin.users.blocks'),'method' => 'get']) }}
                <div class="row">
                  <div class="col-md-5 form-group">
                    {{ Form::text('keyword', app('request')->query('keyword'), ['class' => 'form-control','placeholder' => 'Keyword e.g: Name, Email, Phone number']) }}
                  </div>
                  <div class="col-md-3 form-group">
                    <button class="btn btn-success" title="Filter" type="submit"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.users.blocks') }}" class="btn btn-warning" title="Reset"><i class="fa  fa-refresh"></i> Reset</a>
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
                  <th scope="col">@sortablelink('phone_number', 'Phone Number', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">@sortablelink('email', 'Email', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col">@sortablelink('created_at', 'Member Since', ['filter' => 'active, visible'], ['rel' => 'nofollow'])</th>
                  <th scope="col" class="actions" width="10%">Action</th>
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
                  <td>{{$user->phone_number}}</td>
                  <td>{{$user->email}}</td>
                  <td>{{ $user->created_at->format(config('get.ADMIN_DATE_FORMAT')) }}</td>
                  <td class="actions action-btn-tab">
                    @if ($user->is_block == 1)
                    <span class='btn1 btn-block btn-danger btn-xs text-center'>Blocked</span>
                    @else
                    <span class='btn1 btn-block btn-waring btn-xs text-center'>N/A</span>
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