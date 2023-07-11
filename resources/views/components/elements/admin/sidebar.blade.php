@php
$selectedRoutes = Route::currentRouteName();

@endphp

<div class="sidebar">
  <!-- Sidebar user panel (optional) -->
  <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
      <i class="fas fa-2x fa-user-circle"></i>
    </div>
    <div class="info">
      <a class="d-block">{{-- Auth::user()->first_name }} {{ Auth::user()->last_name --}}</a>
    </div>
  </div> -->
  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
      <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ ($selectedRoutes =='admin.dashboard')?'active':'' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>
            Dashboard
          </p>
        </a>
      </li>
      <!-- Users Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.users.show', 'admin.users.index', 'admin.users.reported', 'admin.users.blocks']))?'menu-is-opening menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-users"></i>
          <p>
            Users Manager
            <i class="right fas fa-angle-right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="{{ route('admin.users.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Total User</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.blocks')?'active':'' }}">
            <a href="{{ route('admin.users.blocks') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Blocked By Admin</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.reported')?'active':'' }}">
            <a href="{{ route('admin.users.reported') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Reported Spam</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Payout Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.posts.show', 'admin.posts.index', 'admin.posts.create', 'admin.posts.edit']))?' menu-open':'' }}">
        <a href="{{ route('admin.posts.index') }}" class="nav-link">
          <i class="nav-icon fas fa-money-bill"></i>
          <p>Payout Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Total Payout</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Transfer Error</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Prime Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.posts.show', 'admin.posts.index', 'admin.posts.create', 'admin.posts.edit']))?' menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-money-bill"></i>
          <p>Prime Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Pending Renewal</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Not Prime</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Total Prime</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- User Kyc Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.posts.show', 'admin.posts.index', 'admin.posts.create', 'admin.posts.edit']))?' menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-check-double"></i>
          <p>User Kyc Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Pending kyc</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Mark Re-kyc</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Total Kyc</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Service Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.categories.show', 'admin.categories.index', 'admin.categories.create', 'admin.categories.edit']))?' menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-users"></i>
          <p>Service Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="{{ route('admin.categories.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Business Listing</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Blocked/Spam</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Running Listing</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Ad Listing Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.posts.show', 'admin.posts.index', 'admin.posts.create', 'admin.posts.edit']))?'menu-is-opening menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-puzzle-piece"></i>
          <p>Ad Listing Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Total Ad</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Deleted by Admin</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Reported Spam</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Marketing Manager -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'menu-is-opening menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-lightbulb"></i>
          <p>Marketing Manager</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Admin Status</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Featured Ad</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>For Prime User</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Contact Us -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'menu-is-opening menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-envelope"></i>
          <p>Contact Us</p>
        </a>
      </li>
      <!-- Personal Data -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'menu-is-opening menu-open':'' }}">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-envelope"></i>
          <p>Personal Data</p>
          <i class="right fas fa-angle-right"></i>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Contact List</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Saved Product</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.users.index')?'active':'' }}">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Saved Customer</p>
            </a>
          </li>
        </ul>
      </li>
      <!-- Settings -->
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'menu-is-opening menu-open':'' }}">
        <a href="{{ route('admin.settings.index') }}" class="nav-link">
          <i class="nav-icon fas fa-cogs"></i>
          <p>Settings</p>
        </a>
      </li>

      {{--
      @can('check-user', "email_templates-index")
      <li class="nav-item {{ in_array($selectedRoutes, ['admin.hooks.index','admin.hooks.create','admin.hooks.edit','admin.hooks.show','admin.email-preferences.index','admin.email-preferences.create','admin.email-preferences.edit','admin.email-preferences.show', 'admin.email-templates.index','admin.email-templates.create','admin.email-templates.edit','admin.email-templates.show']) ? 'menu-is-opening menu-open' : '' }}">
      <a href="#" class="nav-link {{ in_array($selectedRoutes, ['admin.hooks.index','admin.hooks.create','admin.hooks.edit','admin.hooks.show','admin.email-preferences.index','admin.email-preferences.create','admin.email-preferences.edit','admin.email-preferences.show', 'admin.email-templates.index','admin.email-templates.create','admin.email-templates.edit','admin.email-templates.show']) ? 'active' : '' }}">
        <i class="nav-icon fas fa-envelope"></i>
        <p>
          Email Templates
          <i class="right fas fa-angle-right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ in_array($selectedRoutes, ['admin.hooks.index','admin.hooks.create','admin.hooks.edit','admin.hooks.show']) ? 'active' : '' }}">
          <a href="{{ route('admin.hooks.index')}}" class="nav-link">
            <i class="far fa-circle nav-icon"></i> Hooks (slugs)
          </a>
        </li>
        <li class="nav-item {{ in_array($selectedRoutes, ['admin.email-preferences.index','admin.email-preferences.create','admin.email-preferences.edit','admin.email-preferences.show']) ? 'active' : '' }}">
          <a href="{{ route('admin.email-preferences.index') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i> Email Preferences (layouts)
          </a>
        </li>
        <li class="nav-item {{ in_array($selectedRoutes, ['admin.email-templates.index','admin.email-templates.create','admin.email-templates.edit','admin.email-templates.show']) ? 'active' : '' }}">
          <a href="{{ route('admin.email-templates.index') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i> Email Templates
          </a>
        </li>
      </ul>
      </li>
      @endcan
      --}}

      {{--
      @can('check-user', "locations-index")
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.locations.index', 'admin.locations.create', 'admin.locations.edit', 'admin.locations.show', 'admin.cities.index', 'admin.cities.create', 'admin.cities.edit', 'admin.cities.show', 'admin.areas.index', 'admin.areas.create', 'admin.areas.edit', 'admin.areas.show']))?'menu-is-opening menu-open':'' }}">
      <a href="#" class="nav-link {{ (in_array($selectedRoutes, ['admin.locations.index', 'admin.locations.create', 'admin.locations.edit', 'admin.locations.show', 'admin.cities.index', 'admin.cities.create', 'admin.cities.edit', 'admin.cities.show', 'admin.areas.index', 'admin.areas.create', 'admin.areas.edit', 'admin.areas.show']))?'active':'' }}">
        <i class="nav-icon fas fa-search-location"></i>
        <p>
          Locations Manager
          <i class="right fas fa-angle-right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item {{ (in_array($selectedRoutes, ['admin.cities.index', 'admin.cities.create', 'admin.cities.edit', 'admin.cities.show'])) ? 'active' : '' }}">
          <a href="{{ route('admin.cities.index') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Cities</p>
          </a>
        </li>
        <!-- <li class="nav-item {{ (in_array($selectedRoutes, ['admin.areas.index', 'admin.areas.create', 'admin.areas.edit', 'admin.areas.show'])) ? 'active' : '' }}">
            <a href="{{ route('admin.areas.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Areas</p>
            </a>
          </li> -->
        <li class="nav-item {{ (in_array($selectedRoutes,['admin.locations.index', 'admin.locations.create', 'admin.locations.edit', 'admin.locations.show'])) ? 'active' : '' }}">
          <a href="{{ route('admin.locations.index') }}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Locations</p>
          </a>
        </li>
      </ul>
      </li>
      @endcan
      --}}

      {{--
      @can('check-user', "settings-index")
      <li class="nav-item {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'menu-is-opening menu-open':'' }}">
      <a href="{{ route('admin.settings.index') }}" class="nav-link {{ (in_array($selectedRoutes, ['admin.settings.show', 'admin.settings.index', 'admin.settings.create', 'admin.settings.edit']))?'active':'' }}">
        <i class="fas fa-cogs nav-icon"></i>
        <p>General Settings</p>
      </a>
      <!-- <ul class="nav nav-treeview">
          <li class="nav-item  {{ ($selectedRoutes =='admin.settings.index')?'active':'' }}">
            <a href="{{ route('admin.settings.index') }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>General Settings</p>
            </a>
          </li>
          <li class="nav-item {{ ($selectedRoutes =='admin.settings.create')?'active':'' }}">
            <a href="{{ route("admin.settings.create") }}" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Add General Settings</p>
            </a>
          </li>
        </ul> -->
      </li>
      @endcan
      --}}
      {{-- <li class="nav-item">
        <a href="{{ route("admin.logout") }}" class="nav-link">
      <i class="fas fa-sign-out-alt"></i>
      <p>
        Logout
      </p>
      </a>
      </li> --}}

    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
<style>
  .user-panel .image {
    color: #fff;
  }
</style>