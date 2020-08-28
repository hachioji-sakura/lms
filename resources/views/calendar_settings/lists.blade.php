@section('title')
  {{$domain_name}} {{__('labels.list')}}
@endsection

@section('list_filter')
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
            @if(isset($filter['calendar_filter']['search_week']) && in_array($index, $filter['calendar_filter']['search_week'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="search_work" class="w-100">
        {{__('labels.work')}}
      </label>
      <div class="w-100">
        <select name="search_work[]" class="form-control select2" width=100%  multiple="multiple" >
          @foreach($attributes['work'] as $index=>$name)
            <option value="{{$index}}"
            @if(isset($filter['calendar_filter']['search_work']) && in_array($index, $filter['calendar_filter']['search_work'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="search_place" class="w-100">
        {{__('labels.place')}}
      </label>
      <div class="w-100">
        <select name="search_place[]" class="form-control select2" width=100%  multiple="multiple" >
          @foreach($attributes['places'] as $place)
            <option value="{{$place->id}}"
            @if(isset($filter['calendar_filter']['search_place']) && in_array($place->id, $filter['calendar_filter']['search_place'])==true)
            selected
            @endif
            >{{$place->name()}}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-4">
      <label for="search_status" class="w-100">
        ステータス
      </label>
      <div class="w-100">
        <select name="search_status[]" class="form-control select2" width=100% placeholder="検索曜日" multiple="multiple" >
          @foreach(config('attribute.setting_status') as $index=>$name)
            <option value="{{$index}}"
            @if(isset($filter['calendar_filter']['search_status']) && in_array($index, $filter['calendar_filter']['search_status'])==true)
            selected
            @endif
            >{{$name}}</option>
          @endforeach
        </select>
      </div>
    </div>
    @endslot
  @endcomponent
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @if(isset($teacher_id) && $teacher_id>0)
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create?teacher_id={{$teacher_id}}" class="nav-link">
    @else
    <a href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
    @endif
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
      <li class="nav-item">
       <a href="/{{$domain}}" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>すべて
       </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?search_status[]=new&search_status[]=confirm" class="nav-link
        @if(isset($filter['calendar_filter']['search_status']) && in_array('new', $filter['calendar_filter']['search_status'])==true && in_array('confirm', $filter['calendar_filter']['search_status'])==true)
         active
        @endif
        ">
          <i class="fa fa-exclamation-triangle nav-icon"></i>未確定
        </a>
      </li>
      <li class="nav-item">
         <a href="/{{$domain}}?search_status[]=fix" class="nav-link
         @if(isset($filter['calendar_filter']['search_status']) && in_array('fix', $filter['calendar_filter']['search_status'])==true)
          active
         @endif
         ">
           <i class="fa fa-check-circle nav-icon"></i>確定
         </a>
       </li>
      <li class="nav-item">
      <a href="/{{$domain}}?search_status[]=cancel" class="nav-link
      @if(isset($filter['calendar_filter']['search_status']) && in_array('cancel', $filter['calendar_filter']['search_status'])==true)
       active
      @endif
      ">
        <i class="fa fa-ban nav-icon"></i>キャンセル
      </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    @if(isset($teacher_id) && $teacher_id>0)
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}} {{__('labels.add')}}" page_form="dialog" page_url="{{$domain}}/create?teacher_id={{$teacher_id}}">
    @else
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}} {{__('labels.add')}}" page_form="dialog" page_url="{{$domain}}/create">
    @endif
      <i class="fa fa-plus"></i>{{$domain_name}} {{__('labels.add')}}
    </a>
  </dt>
@endsection

@section('list_pager')
@component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
  @slot("addon_button")
  @endslot
@endcomponent
@endsection


@section('contents')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">@yield('title')</h3>
    <div class="card-title text-sm">
      @yield('list_pager')
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
    @endcomponent
  </div>
</div>
@yield('list_filter')
@endsection

@extends('dashboard.common')
