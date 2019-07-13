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
  @if($user->role==="student")
  <li class="nav-item">
    <a alt="student_name" href="/students/{{$user->id}}/calendar" class="nav-link">
      <i class="fa fa-calendar-alt"></i>
      <span class="d-none d-sm-inline-block">{{__('labels.calendar_page')}}</span>
    </a>
  </li>
  <li class="nav-item">
    <a alt="student_name" href="/students/{{$user->id}}/schedule" class="nav-link">
      <i class="fa fa-clock"></i>
      <span class="d-none d-sm-inline-block">{{__('labels.schedule_list')}}</span>
    </a>
  </li>
  @elseif($user->role==="teacher")
    <li class="nav-item">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-clock"></i>
        <span class="d-none d-sm-inline-block">{{__('labels.schedule_list')}}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/teachers/{{$user->id}}/calendar" class="dropdown-item">{{__('labels.calendar_page')}}</a>
        <a href="/teachers/{{$user->id}}/schedule?list=today" class="dropdown-item">{{__('labels.today_schedule_list')}}</a>
        <a href="/teachers/{{$user->id}}/schedule?list=confirm" class="dropdown-item">{{__('labels.adjust_schedule_list')}}</a>
        <a href="/teachers/{{$user->id}}/schedule?list=cancel" class="dropdown-item">{{__('labels.rest_schedule_list')}}</a>
        <a href="/teachers/{{$user->id}}/schedule?list=history" class="dropdown-item">{{__('labels.schedule_history')}}</a>
        <a class="dropdown-item" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?teacher_id={{$user->id}}" page_title="{{__('labels.schedule_add')}}">{{__('labels.schedule_add')}}</a>
      </div>
    </li>
  @elseif($user->role==="manager")
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-users"></i>
        <span class="d-none d-sm-inline-block">{{__('labels.account')}}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/students" class="dropdown-item">{{__('labels.students_list')}}</a>
        <a href="/parents" class="dropdown-item">{{__('labels.parents_list')}}</a>
        <a href="/teachers" class="dropdown-item">{{__('labels.teachers_list')}}</a>
        <a href="/managers" class="dropdown-item">{{__('labels.officers_list')}}</a>
      </div>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-database"></i>
        <span class="d-none d-sm-inline-block">{{__('labels.other')}}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg">
        <a href="/trials" class="dropdown-item">{{__('labels.trials_list')}}</a>
        <a href="/comments" class="dropdown-item">{{__('labels.comments_list')}}</a>
        <a href="/milestones" class="dropdown-item">{{__('labels.milestones_list')}}</a>
        <a href="/attributes" class="dropdown-item">{{__('labels.attributes_list')}}</a>
        <a href="/events" class="dropdown-item">{{__('labels.events_list')}}</a>
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
      <span class="d-none d-sm-inline-block">{{$user->name}}</span>
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
      <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.account_setting')}}" page_form="dialog" page_url="/managers/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>{{__('labels.account_setting')}}
      </a>
      @elseif($user->role==="teacher")
      <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.account_setting')}}" page_form="dialog" page_url="/teachers/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>{{__('labels.account_setting')}}
      </a>
      </small>
      @elseif($user->role==="parent")
      @elseif($user->role==="student")
      <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.account_setting')}}" page_form="dialog" page_url="/students/{{$user->id}}/edit" >
        <i class="fa fa-user-edit mr-2"></i>{{__('labels.account_setting')}}
      </a>
      @endif
      <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.password_setting')}}" page_form="dialog" page_url="/password" >
        <i class="fa fa-lock mr-2"></i>{{__('labels.password_setting')}}
      </a>
      <a href="/faqs" class="dropdown-item" >
        <i class="fa fa-question-circle mr-2"></i>{{__('labels.faq')}}
      </a>
      @if(app()->getLocale()=='en')
      <a href="/home?locale=ja" class="dropdown-item" >
        <i class="fa fa-exchange-alt mr-2"></i>日本語
      </a>
      @else
      <a href="/home?locale=en" class="dropdown-item" >
        <i class="fa fa-exchange-alt mr-2"></i>English
      </a>
      @endif
      <div class="dropdown-divider"></div>
      <a href="/logout" class="dropdown-item">
        <i class="fa fa-sign-out-alt mr-2"></i>{{__('labels.logout')}}
      </a>
    </div>
  </li>
</ul>
</nav>
@endif
