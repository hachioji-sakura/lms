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
         <a href="{{request()->fullUrl()}}" class="nav-link">
           <i class="fa fa-calendar nav-icon"></i>すべて
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

@extends('dashboard.common')
@include('dashboard.lists')
