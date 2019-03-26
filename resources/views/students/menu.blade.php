@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name()}}</rb>
          <rt>{{$item->kana()}}</rt>
        </ruby>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/" class="nav-link @if($view=="page") active @endif">
            <i class="fa fa-home nav-icon"></i>トップ
          </a>
        </li>
        <li class="nav-item hr-1 bd-light">
          <a href="/{{$domain}}/{{$item->id}}/calendar" class="nav-link @if($view=="calendar") active @endif">
            <i class="fa fa-calendar-alt nav-icon"></i>カレンダー
          </a>
        </li>
          <li class="nav-item hr-1 bd-light">
          <a href="/{{$domain}}/{{$item ->id}}/schedule" class="nav-link @if($view=="schedule" && $list=="") active @endif">
            <i class="fa fa-calendar-check nav-icon"></i>授業予定
          </a>
        </li>
      </ul>
      <ul class="nav nav-treeview">
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
