@section('page_sidemenu')
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <div class="image mt-1">
    <img src="{{$item['icon']}}" class="img-circle elevation-2" alt="User Image">
  </div>
  <div class="info">
    <a href="/{{$domain}}/{{$item->id}}/" class="d-block text-light">
      <ruby style="ruby-overhang: none">
        <rb>{{$item->name()}}</rb>
        <rt>{{$item->kana()}}</rt>
      </ruby>
      <span class="ml-2">様</span>
    </a>
  </div>
</div>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  @if($item->is_hachiojisakura()==true)
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="nav-link @if($view=="calendar" && $list=="") active @endif">
      <i class="fa fa-calendar-alt nav-icon"></i>{{__('labels.calendar_page')}}
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
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=month" class="nav-link @if($view=="schedule" && $list=="month") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
            {{__('labels.month_schedule_list')}}
            @if($month_count > 0)
            <span class="badge badge-primary right">{{$month_count}}</span>
            @endif
          </p>
        </a>
      </li>
      @if($user->role!='student')
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
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=rest_contact" class="nav-link @if($view=="schedule" && $list=="rest_contact") active @endif">
          <i class="fa fa-calendar-times nav-icon"></i>
          <p>
            {{__('labels.rest_contact')}}
            @if($rest_contact_count > 0)
            <span class="badge badge-danger right">{{$rest_contact_count}}</span>
            @endif
          </p>
        </a>
      </li>
      @endif
      @if($user->role!='student')
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
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=history" class="nav-link @if($view=="schedule" && $list=="history") active @endif">
          <i class="fa fa-history nav-icon "></i>
          {{__('labels.schedule_history')}}
        </a>
      </li>
      @endif
    </ul>
  </li>
  @endif
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-pen-nib"></i>
    <p>
      {{__('labels.study_record')}}
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.milestones')}}{{__('labels.add')}}">
          <i class="fa fa-flag nav-icon"></i>{{__('labels.milestones')}}{{__('labels.add')}}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
          <i class="fa fa-comment-dots nav-icon"></i>{{__('labels.comment_add')}}
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
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{__('labels.students')}}{{__('labels.setting')}}">
          <i class="fa fa-user-edit nav-icon"></i>{{__('labels.students')}}{{__('labels.setting')}}
        </a>
      </li>
      @if($item->is_hachiojisakura()==true)
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/setting" page_title="{{__('labels.students')}}{{__('labels.setting')}}">
          <i class="fa fa-business-time nav-icon"></i>通塾設定
        </a>
      </li>
      @endif
      @if($user->role==="manager")
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/tag" page_title="{{__('labels.tags')}}{{__('labels.setting')}}">
          <i class="fa fa-tags nav-icon"></i>{{__('labels.tags')}}{{__('labels.setting')}}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:window.open('/{{$domain}}/{{$item->id}}/tuition', null, 'width=1024,height=640,toolbar=no,menubar=no,scrollbars=no');">
          <i class="fa fa-file-invoice-dollar nav-icon"></i>受講料設定
        </a>
      </li>
      @endif
      {{--
      @if($user->role==="parent")
      <li class="nav-item">
        <a class="nav-link" href="/{{$domain}}/{{$item->id}}/unsubscribe">
          <i class="fa fa-tags nav-icon"></i>{{__('labels.recess_or_unsubscribe')}}
        </a>
      </li>
      @endif
      --}}
    </ul>
  </li>
</ul>
@if($user->role==="manager" || $user->role==="teacher")
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-user-friends"></i>
    <p>
      ご契約者様
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      @foreach($item->relations as $relation)
      <li class="nav-item">
        <a class="nav-link" href="/parents/{{$relation->student_parent_id}}">
          <i class="fa fa-user nav-icon"></i>{{$relation->parent->name()}}
        </a>
      </li>
      @endforeach
    </ul>
  </li>
</ul>
@endif
@endsection
@section('page_footer')
{{--  Footerに動作ボタンは入れない？？
<dt>
  <a class="btn btn-app" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?origin={{$domain}}&item_id={{$item->id}}" page_title="目標登録">
    <i class="fa fa-flag"></i>{{__('labels.milestones')}}{{__('labels.setting')}}
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
<dt>
  <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.tasks').__('labels.add')}}" page_url="/tasks/create?student_id={{$item->id}}" title="{{__('labels.add_button')}}" role="button" class="btn btn-app">
      <i class="fa fa-tasks"></i>{{__('labels.tasks')}}{{__('labels.add')}}
    </a>
</dt>
--}}
@endsection
