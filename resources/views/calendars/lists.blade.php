@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @if(isset($teacher_id) && $teacher_id>0)
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create?teacher_id={{$teacher_id}}" class="nav-link">
    @else
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
    @endif
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
         <a href="{{request()->fullUrl()}}" class="nav-link">
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
  <div class="card-tools pt-2">
    @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count]) @endcomponent
  </div>
</div>
<div class="card-body table-responsive p-0">
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-4">
      <label for="search_week" class="w-100">
        曜日
      </label>
      <div class="w-100">
        <select name="search_week[]" class="form-control select2" width=100% placeholder="検索曜日" multiple="multiple" >
          @foreach($attributes['lesson_week'] as $index=>$name)
            <option value="{{$index}}"
            @if(isset($filter['search_week']) && in_array($index, $filter['search_week'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="search_work" class="w-100">
        作業
      </label>
      <div class="w-100">
        <select name="search_work[]" class="form-control select2" width=100% placeholder="検索作業" multiple="multiple" >
          @foreach($attributes['work'] as $index=>$name)
            <option value="{{$index}}"
            @if(isset($filter['search_work']) && in_array($index, $filter['search_work'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="search_place" class="w-100">
        場所
      </label>
      <div class="w-100">
        <select name="search_place[]" class="form-control select2" width=100% placeholder="検索場所" multiple="multiple" >
          @foreach($attributes['places'] as $place)
            @foreach($place->floors as $floor)
            <option value="{{$floor->id}}"
            @if(isset($filter['search_place']) && in_array($floor->id, $filter['search_place'])==true)
            selected
            @endif
            >{{$floor->name}}</option>
            @endforeach
          @endforeach
        </select>
      </div>
    @endslot
  @endcomponent

  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>
@endsection


@section('page_footer')
  <dt>
    @if(isset($teacher_id) && $teacher_id>0)
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create?teacher_id={{$teacher_id}}">
    @else
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
    @endif
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
@endsection
