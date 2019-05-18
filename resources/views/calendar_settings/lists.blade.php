@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

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
        フィルタ
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
         <a href="/{{$domain}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>すべて
         </a>
       </li>
    </ul>
  </li>
</ul>
@endsection


@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
  <div class="card-tools">
    <a class="btn btn-default btn-sm mr-2" role="button" data-toggle="collapse" data-parent="#filter_form" href="#filter_form_item" class="" aria-expanded="true">
      <i class="fa fa-filter mr-1"></i>絞込
    </a>
  </div>
</div>
<div class="card-body table-responsive p-0">
  <div id="filter_form" class="container-fluid">
    <form method="POST" action="/{{$domain}}">
      @method('GET')
		  <div class="row collapse p-2" id="filter_form_item">
			<div class="col-4">
        <label for="charge_subject" class="w-100">
          曜日
        </label>
        <div class="w-100">
          <select name="search_week[]" class="form-control" width=100% placeholder="検索曜日" multiple="multiple" >
            <option value="">(選択)</option>
            @foreach($attributes['lesson_week'] as $index=>$name)
              <option value="{{$index}}"
              @if(isset($search_week) && in_array($index, $search_week)==true)
              selected
              @endif
              >{{$name}}</option>
            @endforeach
          </select>
        </div>
			</div>
      <div class="col-4">
        <label for="charge_subject" class="w-100">
          作業
        </label>
        <div class="w-100">
          <select name="search_work[]" class="form-control" width=100% placeholder="検索作業" multiple="multiple" >
            <option value="">(選択)</option>
            @foreach($attributes['work'] as $index=>$name)
              <option value="{{$index}}"
              @if(isset($search_work) && in_array($index, $search_work)==true)
              selected
              @endif
              >{{$name}}</option>
            @endforeach
          </select>
        </div>
			</div>
      <div class="col-4">
        <label for="charge_subject" class="w-100">
          場所
        </label>
        <div class="w-100">
          <select name="search_place[]" class="form-control" width=100% placeholder="検索場所" multiple="multiple" >
            <option value="">(選択)</option>
            @foreach($attributes['lesson_place_floor'] as $index=>$name)
              <option value="{{$index}}"
              <option value="{{$index}}"
              @if(isset($search_place) && in_array($index, $search_place)==true)
              selected
              @endif
              >{{$name}}</option>
            @endforeach
          </select>
        </div>
			</div>
      <div class="col-12 mt-2 text-right">
          <button type="button" class="btn btn-submit btn-info mr-2"  accesskey="filter_search">
            <i class="fa fa-search mr-1"></i>
              絞り込み
          </button>
          <button type="reset" class="btn btn-secondary" accesskey="filter_search">
              クリア
          </button>
      </div>
    </div>
    </form>
	</div>

  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>


<script>
$(function(){
  base.pageSettinged('filter_form', null);
  $("button[accesskey='filter_search'][type=button]").on('click', function(){
    $("#filter_form form").submit();
  });
  $("button[accesskey='filter_search'][type=reset]").on('click', function(){
    $("#filter_form form select").val("");
    $("#filter_form form select").val(-1).trigger('change');
  });
});
</script>

@endsection


@section('page_footer')
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
