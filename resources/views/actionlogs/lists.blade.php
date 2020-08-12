@section('title')
{{__('labels.actionlogs')}}
@endsection
@section('title_header')
{{__('labels.actionlogs')}}
@endsection

@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-12 col-md-4">
      <div class="form-group">
        <label for="is_asc_1" class="w-100">
          {{__('labels.sort_no')}}
        </label>
        <label class="mx-2">
        <input type="checkbox" value="1" name="is_asc" class="icheck flat-green" id="is_asc_1"
        @if(isset($filter['sort']['is_asc']) && $filter['sort']['is_asc']==true)
          checked
        @endif
        >{{__('labels.date')}} {{__('labels.asc')}}
        </label>
      </div>
    </div>
    <div class="col-12 mb-2">
      <label for="search_type" class="w-100">
        HTTP METHOD
      </label>
      <div class="w-100">
        @foreach(config('attribute.http_method') as $index=>$name)
          <label class="mx-2">
          <input type="checkbox" value="{{$index}}" name="search_type[]" class="icheck flat-green"
            @if(isset($filter['calendar_filter']['search_type']) && in_array($index, $filter['calendar_filter']['search_type'])==true)
            checked
            @endif
            >{{$name}}
          </label>
        @endforeach
      </div>
    </div>
    <div class="col-12 mb-2">
        <label for="session_id" class="w-100">
          SESSION ID
        </label>
        <input type="text" name="session_id" class="form-control" placeholder="" inputtype=""
        @isset($filter['session_id'])
        value = "{{$filter['session_id']}}"
        @endisset
        >
    </div>
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
    @endslot
  @endcomponent
@endsection

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
         <a href="/{{$domain}}" class="nav-link ">
           <i class="fa fa-list-alt nav-icon"></i>すべて
         </a>
       </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
  <dt>
    {{--
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="{{$domain}}/create">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
    --}}
  </dt>
@endsection

@extends('dashboard.common')
@include('dashboard.lists')
