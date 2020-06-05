@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link @if(!isset($_status)) active @endif">
          <i class="fa fa-users nav-icon"></i>すべて
        </a>
      </li>
      @foreach(config('attribute.student_status') as $index => $name)
      @if($index=='recess') @continue @endif
      <li class="nav-item">
        <a href="/{{$domain}}?status={{$index}}" class="nav-link @if($_status==$index) active @endif">
          <i class="fa fa-list-alt nav-icon"></i>{{$name}}
        </a>
      </li>
      @endforeach
    </ul>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{$domain_name}}登録
  </a>
</dt>
--}}
@endsection


@section('list_filter')
@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
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
  'fields' => [],
])
@endcomponent
@endsection
