@section('title')
{{ $domain_name }}
@endsection

@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-12 mb-2">
        <label for="search_word" class="w-100">
          {{__('labels.search_keyword')}}
        </label>
        <input type="text" name="search_word" class="form-control" placeholder="" inputtype=""
        @isset($filter['search_keyword'])
        value = "{{$filter['search_keyword']}}"
        @endisset
        >
    </div>
    <div class="col-12 mb-2">
      <label for="search_word" class="w-100">
        {{__('labels.school_page_header_process')}}
      </label>
      <div class="input-group">
        @foreach($processes as $key => $value)
        <div class="form-check">
          <input class="frm-check-input icheck flat-green" type="radio" name="process"  value="{{$key}}"  id="process_{{$key}}" {{request()->has('process') && request()->process == $key ? 'checked' : ""}}>
          <label class="form-check-label" for="process_{{$key}}">
            {{$value}}
          </label>
        </div>
        @endforeach
      </div>
    </div>
    @endslot
  @endcomponent
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="学校{{__('labels.add')}}" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>学校{{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>すべて
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=kindergarten" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>幼稚園
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=elementary_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>小学校
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=junior_high_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>中学校
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=high_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>高校
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=special_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>特別学校
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?school_type=nursing_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>看護学校
       </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@extends('dashboard.common')
@include('dashboard.lists')
