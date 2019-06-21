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
    <a href="/{{$domain}}/{{$item->id}}/calendar" class="nav-link @if($view=="calendar" && $list=="") active @endif">
      <i class="fa fa-calendar-alt nav-icon"></i>カレンダー
    </a>
  </li>
  <li class="nav-item has-treeview menu-open">
    <a href="#" class="nav-link">
    <i class="nav-icon fa fa-clock"></i>
    <p>
      スケジュール
      <i class="right fa fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview pl-2">
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=recent" class="nav-link @if($view=="schedule" && $list=="recent") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
            直近予定
            @if($recent_count > 0)
            <span class="badge badge-primary right">{{$recent_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}/{{$item->id}}/schedule?list=confirm" class="nav-link  @if($view=="schedule" && $list=="confirm") active @endif">
          <i class="fa fa-hourglass nav-icon"></i>
          <p>
            予定調整中
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
            休み予定
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
            振替対象
            @if($exchange_count > 0)
            <span class="badge badge-danger right">{{$exchange_count}}</span>
            @endif
          </p>
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
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{$domain_name}}設定">
          <i class="fa fa-user-edit nav-icon"></i>{{$domain_name}}設定
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?origin={{$domain}}&item_id={{$item->id}}" page_title="目標登録">
          <i class="fa fa-flag nav-icon"></i>目標登録
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
          <i class="fa fa-comment-dots nav-icon"></i>コメント登録
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
<dt>
  <a class="btn btn-app" href="javascript:void(0);"  page_form="dialog" page_url="/milestones/create?origin={{$domain}}&item_id={{$item->id}}" page_title="目標登録">
    <i class="fa fa-flag"></i>目標登録
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
{{-- まだ対応しない
  <dt>
    <a class="btn btn-app" href="javascript:void(0);" accesskey="task_add" disabled>
      <i class="fa fa-plus"></i>タスク登録
    </a>
  </dt>
--}}
@endsection
