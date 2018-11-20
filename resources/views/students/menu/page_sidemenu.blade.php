@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-user-graduate"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
        </ruby>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="javascript:void(0);" class="nav-link" accesskey="comment_add">
            <i class="fa fa-comment-dots nav-icon"></i>コメント登録
          </a>
        </li>
        {{--まだ対応しない
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link" accesskey="task_add">
              <i class="fa fa-plus nav-icon"></i>タスク登録
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link" accesskey="milestone_add">
              <i class="fa fa-flag nav-icon"></i>目標登録
            </a>
          </li>
        --}}
      </ul>
    </li>
</ul>
@endsection
