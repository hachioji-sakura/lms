@section('sidemenu')
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <span class="brand-text font-weight-light">学習管理システム</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item has-treeview menu-open">
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
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
@endsection
