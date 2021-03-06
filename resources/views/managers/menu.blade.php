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
      <i class="fa fa-tasks nav-icon"></i>勤務実績
    </a>
  </li>
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="nav-link @if($view=="calendar") active @endif">
      <i class="fa fa-calendar-alt nav-icon"></i>カレンダー
    </a>
  </li>
  @if($user->role==="manager")
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-clock"></i>
    <p>
      業務
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=lecture_cancel" class="nav-link @if($view=="ask" && $list=="lecture_cancel") active @endif">
          <i class="fa fa-calendar-times nav-icon"></i>
          <p>
            休講申請
            @if($lecture_cancel_count > 0)
            <span class="badge badge-danger right">{{$lecture_cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=teacher_change" class="nav-link @if($view=="ask" && $list=="teacher_change") active @endif">
          <i class="fa fa-exchange-alt nav-icon"></i>
          <p>
            代講依頼
            @if($teacher_change_count > 0)
            <span class="badge badge-danger right">{{$teacher_change_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/curriculums" class="nav-link">
          <i class="fa fa-sitemap nav-icon"></i>
          <p>
            単元管理
          </p>
        </a>
      </li>
      {{-- TODO お問い合わせ実装後に有効にする
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=phone" class="nav-link @if($view=="ask" && $list=="phone") active @endif">
          <i class="fa fa-phone nav-icon"></i>
          <p>
            お問い合わせ
            @if($phone_count > 0)
            <span class="badge badge-danger right">{{$phone_count}}</span>
            @endif
          </p>
        </a>
      </li>
      --}}
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=recess" class="nav-link @if($view=="ask" && $list=="recess") active @endif">
          <i class="fa fa-pause-circle nav-icon"></i>
          <p>
            休会連絡
            @if($recess_count > 0)
            <span class="badge badge-danger right">{{$recess_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/ask?list=unsubscribe" class="nav-link @if($view=="ask" && $list=="unsubscribe") active @endif">
          <i class="fa fa-times-circle nav-icon"></i>
          <p>
            退会連絡
            @if($unsubscribe_count > 0)
            <span class="badge badge-danger right">{{$unsubscribe_count}}</span>
            @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
  @endif
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-cogs"></i>
    <p>
      その他
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="勤務設定">
          <i class="fa fa-user-cog nav-icon"></i>勤務設定
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar_settings">
          <i class="fa fa-user-clock nav-icon"></i>シフト一覧
        </a>
      </li>
      @if($user->role==="manager")
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/tag" page_title="権限設定">
          <i class="fa fa-key nav-icon"></i>権限設定
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
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create??origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
--}}
@endsection
