@extends('dashboard.common')

@section('title')
{{$domain_name}}
@endsection

@section('title_header')
{{__('labels.agreement_statements')}}
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open mt-2">
      <a href="#" class="nav-link">
        <i class="nav-icon fa fa-shake_hands"></i>
        <p>
          {{__('labels.agreements')}}
          <i class="right fa fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/create" page_title="{{__('labels.new').__('labels.agreement_statements')}}" class="nav-link">
            <i class="fa fa-plus nav-icon"></i>{{__('labels.new').__('labels.agreement_statements')}}
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
