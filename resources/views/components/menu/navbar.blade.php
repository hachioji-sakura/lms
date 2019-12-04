@include('components.menu.navbar.account_nav')
@include('components.menu.navbar.user_nav')
@include('components.menu.navbar.announcement_nav')
@if(isset($user))
<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
<!-- Left navbar links -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
  </li>
  <li class="nav-item">
    <a href="/" class="nav-link">
      <i class="fa fa-home"></i>
      <span class="d-none d-sm-inline-block">{{__('labels.top')}}</span>
    </a>
  </li>
  @yield('user_nav')
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  {{--
  @yield('announcement_nav')
  --}}
  @yield('account_nav')
</ul>
</nav>
@endif
