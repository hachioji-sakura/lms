@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
        </ruby>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview hr-1 bd-light">
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/" >
            <i class="fa fa-home nav-icon"></i>トップ
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/calendar" >
            <i class="fa fa-calendar-alt nav-icon"></i>カレンダー
          </a>
        </li>
        <li class="nav-item hr-1 bd-light">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/schedule" >
            <i class="fa fa-clock nav-icon"></i>授業予定
          </a>
        </li>
      </ul>
      <ul class="nav nav-treeview">
        {{--
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="コメント登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="授業追加">
            <i class="fa fa-chalkboard-teacher nav-icon"></i>授業追加
          </a>
        </li>
        --}}
      </ul>
    </li>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="コメント登録">
    <i class="fa fa-comment-dots"></i>コメント登録
  </a>
</dt>
--}}
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/calendars/create?_page_origin={{$domain}}_{{$item->id}}&teacher_id={{$item->id}}" page_title="授業追加">
    <i class="fa fa-chalkboard-teacher"></i>授業追加
  </a>
</dt>
@endsection
