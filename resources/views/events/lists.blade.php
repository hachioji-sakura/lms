@section('title')
イベント一覧
@endsection
@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
    <div class="col-6 col-md-4">
      <div class="form-group">
        <label for="search_from_date" class="w-100">
          {{__('labels.date')}}(FROM)
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          </div>
          <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
          @if(isset($filter['calendar_filter']['search_from_date']))
            value="{{$filter['calendar_filter']['search_from_date']}}"
          @endif
          >
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="form-group">
        <label for="search_to_date" class="w-100">
          {{__('labels.date')}}(TO)
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          </div>
          <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
          @if(isset($filter['calendar_filter']['search_to_date']))
            value="{{$filter['calendar_filter']['search_to_date']}}"
          @endif
          >
        </div>
      </div>
    </div>
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
  <li class="nav-item hr-1">
    <a href="/event_templates" class="nav-link">
      <i class="fa fa-calendar-alt nav-icon"></i>{{__('labels.event_templates')}}一覧
    </a>
  </li>

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
      @foreach($attributes['event_status'] as $index => $name)
      @if($index=='new') @continue @endif
      @if($index=='progress')
      <?php
        $index='new,progress';
        $name='実施中(予定含む)';
        ?>
      @endif
      <li class="nav-item">
         <a href="/{{$domain}}?search_status={{$index}}" class="nav-link @if(isset($filter['search_status']) && $index===$filter['search_status']) active @endif">
           <i class="fa fa-list-alt nav-icon"></i>{{$name}}
         </a>
       </li>
       @endforeach
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
@endsection


@extends('dashboard.common')
@include('dashboard.lists')
