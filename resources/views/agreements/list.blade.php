@extends('dashboard.common')

@section('title')
{{$domain_name}}
@endsection

@section('title_header')
{{__('labels.agreements')}}
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
          <a href="/agreements" class="nav-link {{!request()->has('search_status') ? 'active': ''}}">
            <i class="fa fa-list-alt nav-icon"></i>
            {{__('labels.all')}}
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?search_status=new" class="nav-link {{request()->search_status == 'new' ? 'active' : ''}}">
            <i class="fa fa-exclamation-triangle nav-icon"></i>
            {{__('labels.agreement_new')}}
            <span class="badge badge-danger right ml-2">
            {{$new_item_count}}
          </span>
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?search_status=commit" class="nav-link {{request()->search_status == 'commit' ? 'active' : ''}}">
            <i class="fa fa-check-circle nav-icon"></i>
            {{__('labels.agreement_commit')}}
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?search_status=cancel" class="nav-link {{request()->search_status == 'cancel' ? 'active' : ''}}">
            <i class="fa fa-times-circle nav-icon"></i>
            {{__('labels.cancel')}}
          </a>
        </li>
      </ul>
    </li>
  </li>
</ul>
@endsection


@section('page_footer')
@endsection

@section('list_filter')
@component('components.list_filter_message', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
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
  @endslot
@endcomponent
@endsection

@include('dashboard.lists')
