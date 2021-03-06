@section('user_nav')
@if($user->role==="student")
<li class="nav-item">
  <a alt="student_name" href="/students/{{$user->id}}/calendar" class="nav-link">
    <i class="fa fa-calendar-alt"></i>
    <span class="d-none d-sm-inline-block">{{__('labels.calendar_page')}}</span>
  </a>
</li>
<li class="nav-item">
  <a alt="student_name" href="/students/{{$user->id}}/schedule?list=month" class="nav-link">
    <i class="fa fa-clock"></i>
    <span class="d-none d-sm-inline-block">{{__('labels.month_schedule_list')}}</span>
  </a>
</li>
@elseif($user->role==="parent")
{{--
<li class="nav-item">
  <a href="/parents/{{$user->id}}/ask" class="nav-link">
    <i class="fa fa-phone"></i>
    <span class="d-none d-sm-inline-block">お問い合わせ一覧</span>
  </a>
</li>
--}}
<li class="nav-item">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="fa fa-user-graduate"></i>
    <span class="d-none d-sm-inline-block">登録生徒</span>
  </a>
  <div class="dropdown-menu dropdown-menu-lg">
    @foreach($user->relation() as $relation)
    <a href="/students/{{$relation->student->id}}" class="dropdown-item">
      {{$relation->student->name()}}
    </a>
    @endforeach
  </div>
</li>
<li class="nav-item">
  <a href="/faqs" class="nav-link">
    <i class="fa fa-question-circle"></i>
    <span class="d-none d-sm-inline-block">{{__('labels.faqs')}}</span>
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
      <i class="fa fa-calendar"></i>
      <span class="d-none d-sm-inline-block">{{__('labels.calendar_page')}}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg">
      <a href="/calendars" class="dropdown-item">{{__('labels.schedule_list')}}</a>
      <a href="/calendar_settings" class="dropdown-item">{{__('labels.repeat_schedule_settings_list')}}</a>
    </div>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
      <i class="fa fa-database"></i>
      <span class="d-none d-sm-inline-block">{{__('labels.other')}}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg">
      <a href="/trials?list=new" class="dropdown-item">{{__('labels.trials_list')}}</a>
      {{--
      <a href="/comments" class="dropdown-item">{{__('labels.comments_list')}}</a>
      <a href="/milestones" class="dropdown-item">{{__('labels.milestones_list')}}</a>
      --}}

      <a href="/agreements?search_status=commit&search_enable=1" class="dropdown-item">{{__('labels.agreements').__('labels.list')}}</a>
      <a href="/events" class="dropdown-item">{{__('labels.events_list')}}</a>
      <a href="/text_materials" class="dropdown-item">{{__('labels.text_materials')}}</a>
      <a href="/maillogs?search_status[]=sended" class="dropdown-item">{{__('labels.maillogs')}}</a>
      <a href="/actionlogs" class="dropdown-item">{{__('labels.actionlogs')}}</a>
      <a href="/attributes?select_key=keys" class="dropdown-item">{{__('labels.attributes_list')}}</a>
      <a href="/places" class="dropdown-item">{{__('labels.places').__('labels.list')}}</a>
    </div>
  </li>
@endif
@endsection
