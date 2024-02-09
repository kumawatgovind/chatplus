<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ config('get.ADMIN_PAGE_TITLE') }} | @yield('title')</title>
  <link href="{{ asset('favicon.ico') }}" type="image/x-icon" rel="icon" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

  <link href="{{ asset('plugins/jquery-confirm-v3.3.4/css/jquery-confirm.css') }}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/admin/custom.css') }}?v=2">
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css">
  @stack('styles')
  <style>
    .required label::after {
      color: #cc0000;
      content: "*";
      font-weight: bold;
      margin-left: 5px;
    }

    label.rmstrict::after {
      content: "";
      font-weight: bold;
      margin-left: 5px;
    }
  </style>
  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Preloader -->
    <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('dist/img/ajax-loader.gif') }}" alt="AdminLTELogo" height="60" width="60">
  </div> -->
    <div class="overlay-block loader hide">
      <div class="overlay-block-inr">
        <div class="loader-block text-center dis-block clearfix">
          <div class="ldr-img"><img src="{{ asset('img/loader.gif') }}">Loading...</div>
        </div>
      </div>
    </div>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <!-- <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2" alt="User Image"> -->
            <span class="d-none d-md-inline">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <!-- User image -->
            <li class="user-header bg-primary">
              <img src="{{ asset('img/logo.jpg') }}" class="img-circle elevation-2" alt="User Image">
              <p>
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                <small>User since {{ Auth::user()->created_at->format('M. Y') }}</small>
              </p>
            </li>
            <!-- Menu Body -->
            <li class="user-body">
              <div class="row">
                <div class="col-4 text-center">
                  <a href="{{ route('frontend.clear-data') }}" onclick="return confirm('Are you sure. You want to clear all data?')">Clear Data</a>
                </div>
                {{--
                @can('check-user', "settings-index")
                <div class="col-4 text-center">
                  <a href="{{ route('admin.settings.index') }}">Settings</a>
                </div>
                @endcan
                --}}
                <div class="col-8 text-center">
                  <a href="{{ route('admin.change-password.index') }}">Change Password</a>
                </div>
              </div>
              <!-- /.row -->
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <a href="{{ route('admin.profile') }}" class="btn btn-default btn-flat">Profile</a>
              <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat float-right">Sign out</a>
            </li>
          </ul>
        </li>
      </ul>
      {{-- <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
          <i class="fas fa-2x fa-user-circle"></i>
          <span>{{ Auth::user()->first_name }}</span>

      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        <a href="{{ route('admin.profile') }}" class="dropdown-item">
          <!-- Message Start -->
          <div class="media">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                My Profile
              </h3>
            </div>
          </div>
          <!-- Message End -->
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.change-password.index') }}" class="dropdown-item">
          <!-- Message Start -->
          <div class="media">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                Change Password
              </h3>
            </div>
          </div>
          <!-- Message End -->
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.logout') }}" class="dropdown-item">
          <!-- Message Start -->
          <div class="media">
            <div class="media-body">
              <h3 class="dropdown-item-title">
                Logout
              </h3>
            </div>
          </div>
          <!-- Message End -->
        </a>
      </div>
      </li>
      </ul> --}}

    </nav>
    <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    {{-- sidebar-mini layout-navbar-fixed  --}}
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <x-elements.admin.logo logo="{{ asset('img/logo.jpg') }}"></x-elements.logo>

        <!-- Sidebar -->
        <x-elements.admin.sidebar />
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      @include('flash.alert')
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          {{ $breadcrumb ?? '' }}
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          {{ $content ?? '' }}
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; {{ date('Y') }}.</strong>
      All rights reserved.
    </footer>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- Scripts -->
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('dist/js/adminlte.js') }}"></script>
  <script src="{{ asset('dist/js/demo.js') }}"></script>
  <script src="{{ asset('plugins/jquery-confirm-v3.3.4/js/jquery-confirm.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
  @stack('scripts')

</body>

</html>