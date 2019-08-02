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
        <span class="ml-2">様</span>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/agreement" >
            <i class="fa fa-file-invoice nav-icon"></i>ご契約情報
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="コメント登録">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
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
--}}
@endsection
