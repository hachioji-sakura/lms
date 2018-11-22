@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="/@yield('domain')/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>@yield('domain_name')登録
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
    {{-- まだ対応しない
      <li class="nav-item">
        <a href="" class="nav-link">
          <i class="fa fa-chalkboard-teacher nav-icon"></i>担当生徒
        </a>
      </li>
      <li class="nav-item">
        <a href="" class="nav-link">
          <i class="fa fa-user-circle nav-icon"></i>担当以外
        </a>
      </li>
    --}}
    </ul>
  </li>
</ul>
@endsection
