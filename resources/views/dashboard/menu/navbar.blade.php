@section('navbar')
<!-- Navbar -->
<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
<!-- Left navbar links -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
  </li>
  <li class="nav-item">
    <a href="/" class="nav-link">HOME</a>
  </li>
  @if($user->role==="manager" || $user->role==="teacher")
  @endif
  @if($user->role==="manager")
    <li class="nav-item">
      <a href="/students" class="nav-link">生徒一覧</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/teachers" class="nav-link">講師一覧</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/managers" class="nav-link">事務一覧</a>
    </li>
  @endif
  {{-- まだ対応しない
    <li class="nav-item d-none d-sm-inline-block">
      <a href="/events" class="nav-link">イベント一覧</a>
    </li>
  --}}
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  {{-- まだ対応しない
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="fa fa-bell"></i>
      <span class="badge badge-warning navbar-badge">15</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <span class="dropdown-item dropdown-header"></span>
      <div class="dropdown-divider"></div>
      <a href="#" class="dropdown-item">
        <i class="fa fa-envelope mr-2"></i>未読のお知らせ
        <span class="float-right text-muted text-sm">3件</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="#" class="dropdown-item">
        <i class="fa fa-clock mr-2"></i>本日のスケジュール
        <span class="float-right text-muted text-sm">2件</span>
      </a>
      <div class="dropdown-divider"></div>
      <a href="#" class="dropdown-item dropdown-footer">すべてのお知らせを確認する</a>
    </div>
  </li>
  --}}
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="fa fa-user-alt"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <a href="javascript:void(0);" class="dropdown-item"  page_title="パスワード設定" page_form="dialog" page_url="/password" >
        <i class="fa fa-lock mr-2"></i>パスワード設定
      </a>
      <div class="dropdown-divider"></div>
      <a href="/logout" class="dropdown-item">
        <i class="fa fa-sign-out-alt mr-2"></i>ログアウト
      </a>
    </div>
  </li>
</ul>
</nav>
<!-- /.Navbar -->
@endsection
