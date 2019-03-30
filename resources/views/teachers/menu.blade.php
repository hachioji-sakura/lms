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
<div class="user-panel mb-5">
  <div class="w-100 p-2 @if($view=="calendar")bg-light @endif">
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="text-light">
      <i class="fa fa-calendar-alt mr-1"></i>カレンダー
    </a>
  </div>
  <div class="w-100 p-2 @if($view=="month_work")bg-light @endif">
    <a href="/{{$domain}}/{{$item->id}}/month_work" class="text-light">
      <i class="fa fa-check-circle mr-1"></i>勤務実績
    </a>
  </div>
</div>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-chalkboard-teacher"></i>
      <p>
        授業スケジュール
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview pl-2">
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/schedule" class="nav-link @if($view=="schedule" && $list=="") active @endif">
            <i class="fa fa-calendar-check nav-icon"></i>授業予定
          </a>
        </li>
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/schedule?list=confirm" class="nav-link  @if($view=="schedule" && $list=="confirm") active @endif">
            <i class="fa fa-hourglass nav-icon"></i>予定調整中
          </a>
        </li>
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/schedule?list=cancel" class="nav-link @if($view=="schedule" && $list=="cancel") active @endif">
            <i class="fa fa-calendar-times nav-icon"></i>休み予定
          </a>
        </li>
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/schedule?list=history" class="nav-link @if($view=="schedule" && $list=="history") active @endif">
            <i class="fa fa-history nav-icon "></i>授業履歴
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-cogs"></i>
      <p>
        その他
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview pl-2">
        {{--
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        --}}
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?origin={{$domain}}&item_id={{$item->id}}" page_title="授業追加">
            <i class="fa fa-calendar-plus nav-icon"></i>授業追加
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="講師設定">
            <i class="fa fa-user-edit nav-icon"></i>講師設定
          </a>
        </li>
        @if($user->role==="manager")
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/tag" page_title="タグ設定">
            <i class="fa fa-tags nav-icon"></i>タグ設定
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
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?origin={{$domain}}&item_id={{$item->id}}" page_title="授業追加">
    <i class="fa fa-chalkboard-teacher"></i>授業追加
  </a>
</dt>
--}}
@endsection
