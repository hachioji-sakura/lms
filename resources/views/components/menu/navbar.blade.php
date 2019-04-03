<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
<!-- Left navbar links -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
  </li>
  <li class="nav-item">
    <a href="/" class="nav-link">
      <i class="fa fa-home"></i>
      <span class="d-none d-sm-inline-block">トップ</span>
    </a>
  </li>
  @if($user->role==="student")
  <li class="nav-item">
    <a href="/students/{{$user->id}}/calendar" class="nav-link">
      <i class="fa fa-calendar-alt"></i>
      <span class="d-none d-sm-inline-block">カレンダー</span>
    </a>
  </li>
  <li class="nav-item">
    <a href="/students/{{$user->id}}/schedule" class="nav-link">
      <i class="fa fa-clock"></i>
      <span class="d-none d-sm-inline-block">授業予定</span>
    </a>
  </li>
  @elseif($user->role==="teacher")
    <li class="nav-item">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-clock"></i>
        <span class="d-none d-sm-inline-block">スケジュール</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/teachers/{{$user->id}}/calendar" class="dropdown-item">カレンダー</a>
        <a href="/teachers/{{$user->id}}/schedule" class="dropdown-item">直近予定</a>
        <a href="/teachers/{{$user->id}}/schedule?list=confirm" class="dropdown-item">予定調整中</a>
        <a href="/teachers/{{$user->id}}/schedule?list=cancel" class="dropdown-item">休み予定</a>
        <a href="/teachers/{{$user->id}}/schedule?list=history" class="dropdown-item">授業履歴</a>
        <a class="dropdown-item" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?origin={{$domain}}&teacher_id={{$user->id}}" page_title="授業追加">授業追加</a>
      </div>
    </li>
  @elseif($user->role==="manager")
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-users"></i>
        <span class="d-none d-sm-inline-block">アカウント</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/students" class="dropdown-item">生徒一覧</a>
        <a href="/parents" class="dropdown-item">契約者一覧</a>
        <a href="/teachers" class="dropdown-item">講師一覧</a>
        <a href="/managers" class="dropdown-item">事務一覧</a>
      </div>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-database"></i>
        <span class="d-none d-sm-inline-block">その他</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/trials" class="dropdown-item">体験申込一覧</a>
        <a href="/comments" class="dropdown-item">コメント一覧</a>
        <a href="/milestones" class="dropdown-item">目標一覧</a>
        <a href="/attriubtes" class="dropdown-item">属性一覧</a>
        <a href="/events" class="dropdown-item">イベント一覧</a>
      </div>
    </li>
  @endif
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
      {{--
      <i class="fa fa-user-alt"></i>
      --}}
      <img src="{{$user->icon}}" class="img-size-32 mr-1 img-circle">
      {{$user->name}}
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      {{--
      <a href="#" class="dropdown-item">
        <div class="media">
          <img src="{{$user->icon}}" class="img-size-50 mr-3 img-circle">
          <div class="media-body">
            <h3 class="dropdown-item-title">
              {{$user->name}}
            </h3>
            <p class="text-sm">
              @if($user->role==="manager")
              <small class="badge badge-danger mt-1 mr-1">
              事務
              </small>
              @elseif($user->role==="teacher")
              <small class="badge badge-info mt-1 mr-1">
              講師
              </small>
              @elseif($user->role==="parent")
              <small class="badge badge-info mt-1 mr-1">
              保護者
              </small>
              @elseif($user->role==="student")
              <small class="badge badge-info mt-1 mr-1">
              生徒
              </small>
              @endif
            </p>
          </div>
        </div>
      </a>
      --}}
      @if($user->role==="manager")
      <a href="javascript:void(0);" class="dropdown-item"  page_title="アカウント設定" page_form="dialog" page_url="/managers/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>アカウント設定
      </a>
      @elseif($user->role==="teacher")
      <a href="javascript:void(0);" class="dropdown-item"  page_title="アカウント設定" page_form="dialog" page_url="/teachers/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>アカウント設定
      </a>
      </small>
      @elseif($user->role==="parent")
      @elseif($user->role==="student")
      <a href="javascript:void(0);" class="dropdown-item"  page_title="アカウント設定" page_form="dialog" page_url="/students/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>アカウント設定
      </a>
      @endif
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
