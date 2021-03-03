@section('title')
  {{$domain_name}} {{__('labels.list')}}
@endsection

@section('list_filter')
  @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
    @slot("search_form")
      @component('calendars.filter', ['domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'filter'=>$filter, 'is_list' => true])
      @endcomponent
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
          <a href="/{{$domain}}?search_status[]=absence&search_status[]=rest&search_status[]=lecture_cancel" class="nav-link
       @if(isset($filter['calendar_filter']['search_status']) && in_array('absence', $filter['calendar_filter']['search_status'])==true && in_array('rest', $filter['calendar_filter']['search_status'])==true && in_array('lecture_cancel', $filter['calendar_filter']['search_status'])==true)
            active
@endif
            ">
            <i class="fa fa-calendar-times nav-icon"></i>休み
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
  <div class="card-title text-sm">
    {{$items->appends(Request::query())->links('components.paginate')}}
  </div>
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
