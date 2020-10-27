@section('page_sidemenu')
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <div class="image mt-1">
    <img src="{{$item['icon']}}" class="img-circle elevation-2" alt="User Image">
  </div>
  <div class="info">
    <a href="/{{$domain}}/{{$item->id}}/" class="d-block text-light">
      <ruby style="ruby-overhang: none">
        <rb>{{$item->name}}</rb>
        <rt>{{$item->kana}}</rt>
      </ruby>
    </a>
  </div>
</div>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/month_work" class="nav-link @if($view=="month_work") active @endif">
      <i class="fa fa-tasks nav-icon"></i>{{__('labels.work_record')}}
    </a>
  </li>
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="nav-link @if($view=="calendar") active @endif">
      <i class="fa fa-calendar-alt nav-icon"></i>{{__('labels.calendar_page')}}
    </a>
  </li>
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/messages" class="nav-link">
      <i class="fa fa-envelope nav-icon"></i>{{__('labels.message')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-clock"></i>
    <p>
      {{__('labels.schedule_list')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=today" class="nav-link @if($view=="schedule" && $list=="today") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
              {{__('labels.today_schedule_list')}}
            @if($today_count > 0)
            <span class="badge badge-primary right">{{$today_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=confirm" class="nav-link  @if($view=="schedule" && $list=="confirm") active @endif">
          <i class="fa fa-hourglass nav-icon"></i>
          <p>
            {{__('labels.adjust_schedule_list')}}
            @if($confirm_count > 0)
            <span class="badge badge-warning right">{{$confirm_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=cancel" class="nav-link @if($view=="schedule" && $list=="cancel") active @endif">
          <i class="fa fa-calendar-times nav-icon"></i>
          <p>
            {{__('labels.rest_schedule_list')}}
            @if($cancel_count > 0)
            <span class="badge badge-danger right">{{$cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=exchange" class="nav-link @if($view=="schedule" && $list=="exchange") active @endif">
          <i class="fa fa-exchange-alt nav-icon"></i>
          <p>
            {{__('labels.exchange_schedule_list')}}
            @if($exchange_count > 0)
            <span class="badge badge-danger right">{{$exchange_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
          <i class="fa fa-calendar-plus nav-icon"></i>
          {{__('labels.schedule_add')}}
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=history" class="nav-link @if($view=="schedule" && $list=="history") active @endif">
          <i class="fa fa-history nav-icon "></i>
          {{__('labels.schedule_history')}}
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-business-time"></i>
    <p>
      {{__('labels.regular_schedule_setting')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/calendar_settings?list=fix_list" class="nav-link @if($view=="calendar_settings" && $list=="fix_list") active @endif">
          <i class="fa fa-user-clock nav-icon"></i>
          <p>
            {{__('labels.regular_schedule_list')}}
            @if($fix_list_setting_count > 0)
            <span class="badge badge-primary right">{{$fix_list_setting_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/{{$domain}}/{{$item->id}}/calendar_settings?list=confirm_list" class="nav-link @if($view=="calendar_settings" && $list=="confirm_list") active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>
          <p>
            {{__('labels.regular_schedule_confirm')}}
            @if($confirm_list_setting_count > 0)
            <span class="badge badge-warning right">{{$confirm_list_setting_count}}</span>
            @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-envelope"></i>
    <p>
      {{__('labels.asks')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=rest_cancel" class="nav-link @if($view=="ask" && $list=="rest_cancel") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
            {{__('labels.schedule_rest_cancel')}}
            @if($rest_cancel_count > 0)
            <span class="badge badge-danger right">{{$rest_cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=lecture_cancel" class="nav-link @if($view=="ask" && $list=="lecture_cancel") active @endif">
          <i class="fa fa-calendar-times nav-icon"></i>
          <p>
            {{__('labels.ask_lecture_cancel')}}
            @if($lecture_cancel_count > 0)
            <span class="badge badge-danger right">{{$lecture_cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=teacher_change" class="nav-link @if($view=="ask" && $list=="lecture_cancel") active @endif">
          <i class="fa fa-sync nav-icon"></i>
          <p>
            {{__('labels.ask_teacher_change')}}
            @if($teacher_change_count > 0)
            <span class="badge badge-danger right">{{$teacher_change_count}}</span>
            @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-cogs"></i>
    <p>
      {{__('labels.other')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{__('labels.teacher_setting')}}">
          <i class="fa fa-user-cog nav-icon"></i>{{__('labels.teacher_setting')}}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/setting" page_title="{{__('labels.working')}}{{__('labels.setting')}}">
          <i class="fa fa-business-time nav-icon"></i>{{__('labels.working')}}{{__('labels.setting')}}
        </a>
      </li>
      @if($user->role==="manager")
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/tag" page_title="{{__('labels.tags')}}{{__('labels.setting')}}">
          <i class="fa fa-tags nav-icon"></i>{{__('labels.tags')}} {{__('labels.setting')}}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:window.open('/{{$domain}}/{{$item->id}}/tuition', null, 'width=1024,height=640,toolbar=no,menubar=no,scrollbars=no');">
          <i class="fa fa-file-invoice-dollar nav-icon"></i>給与設定
        </a>
      </li>
      @endif
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
    <i class="fa fa-chalkboard-teacher"></i>{{__('labels.schedule_add')}}
  </a>
</dt>
--}}
@endsection
