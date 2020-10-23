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
          <a href="/agreements" class="nav-link {{!request()->has('status') ? 'active': ''}}">
            {{__('labels.all')}}
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?status=new" class="nav-link {{request()->status == 'new' ? 'active' : ''}}">
            {{__('labels.new')}}
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?status=commit" class="nav-link {{request()->status == 'commit' ? 'active' : ''}}">
            {{__('labels.enable')}}
          </a>
        </li>

        <li class="nav-item">
          <a href="/agreements?status=cancel" class="nav-link {{request()->status == 'cancel' ? 'active' : ''}}">
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
@endsection

@include('dashboard.lists')
