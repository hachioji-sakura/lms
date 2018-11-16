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
            <i class="nav-icon fa fa-user-graduate"></i>
            <p>
              <ruby style="ruby-overhang: none">
                <rb>{{$item->name_last}} {{$item->name_first}}</rb>
                <rt>{{$item->kana_last}} {{$item->kana_first}}</rt>
              </ruby>
              <i class="right fa fa-angle-left"></i>
            </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="javascript:void(0);" class="nav-link" accesskey="comment_add">
                  <i class="fa fa-comment-dots nav-icon"></i>コメント登録
                </a>
              </li>
              {{--まだ対応しない
                <li class="nav-item">
                  <a href="javascript:void(0);" class="nav-link" accesskey="task_add">
                    <i class="fa fa-plus nav-icon"></i>タスク登録
                  </a>
                </li>
                <li class="nav-item">
                  <a href="javascript:void(0);" class="nav-link" accesskey="milestone_add">
                    <i class="fa fa-flag nav-icon"></i>目標登録
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
