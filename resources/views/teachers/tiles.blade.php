@section('title')
  @if($domain=='teachers')
  {{__('labels.teachers_list')}}
  @elseif($domain=='managers')
  {{__('labels.officers_list')}}
  @endif
@endsection
@extends('dashboard.common')
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @foreach(config('attribute.teacher_status') as $index => $name)
      <li class="nav-item">
        <a href="/{{$domain}}?status={{$index}}" class="nav-link @if((!isset($_status) && $index=='regular') || ($_status==$index)) active @endif">
          <i class="fa fa-list-alt nav-icon"></i>{{$name}}
        </a>
      </li>
      @endforeach
      <li class="nav-item">
        <a href="/{{$domain}}?status=all" class="nav-link @if($_status=='all') active @endif">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
</ul>
<script>
$(function(){
  base.pageSettinged('sidemenu', null);
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

@section('list_filter')
@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 mb-2">
    <label for="search_lesson" class="w-100">
      {{__('labels.lesson')}}
    </label>
    <div class="w-100">
      <select name="search_lesson[]" class="form-control select2" width=100% placeholder="受講レッスン" multiple="multiple" >
        @foreach($attributes['lesson'] as $index=>$name)
          <option value="{{$index}}"
          @if(isset($filter['user_filter']['search_lesson']) && in_array($index, $filter['user_filter']['search_lesson'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="form-group">
      <label for="is_desc" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
      @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
        checked
      @endif
      >{{__('labels.created')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  <div class="col-12 col-md-8">
      <label for="search_word" class="w-100">
        {{__('labels.search_keyword')}}
      </label>
      <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
      @if(isset($filter['search_keyword']))
      value = "{{$filter['search_keyword']}}"
      @endif
      >
  </div>
  @endslot
@endcomponent
@endsection

@section('contents')
@component('components.tiles', [
  'domain' => $domain, 'search_word'=>$search_word, 'items'=>$items, 'user'=>$user,
  'fields' => ['lesson' => 'primary'],
])
@endcomponent
@endsection
