@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')
@include('dashboard.tiles')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}}登録
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="" class="nav-link">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{$domain_name}}登録
  </a>
</dt>
<script>
$(function(){
  $("#search_button").on("click", function(e){
    var _search_word = $("input[name=search_word]").val();
    if(!util.isEmpty(_search_word)){
      location.href="./{{$domain}}?search_word="+_search_word;
    }
  });
  $("a[page_url]").on("click", function(e){
    $("#subDialog .content-sub-title").html($(this).attr("page_title"));
    $("#subDialog .content-sub-body").load($(this).attr("page_url"), function(){
      base.pageOpen('subDialog');
    });
  });
  $(".btn[type=submit]").on("click", function(){
    if(!front.validateFormValue("edit")) return false;
    $("#edit").submit();
  });
})
</script>
@endsection
