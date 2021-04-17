@section('title')
{{ $domain_name }}
@endsection
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{__('labels.school_page_high_school')}}{{__('labels.add')}}" page_form="dialog" page_url="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{__('labels.school_page_high_school')}}{{__('labels.add')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.school_page_high_school')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_all')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=full_day_grade" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_full_day_grade')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=full_day_credit" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_full_day_credit')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=part_time_grade_night_only" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_part_time_grade_night_only')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=part_time_credit" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_part_time_credit')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=part_time_credit_night_only" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_part_time_credit_night_only')}}
       </a>
      </li>
    </ul>
    <ul class="nav nav-treeview">
      <li class="nav-item">
       <a href="/{{$domain}}?process=online_school" class="nav-link">
         <i class="fa fa-calendar nav-icon"></i>{{__('labels.school_page_side_menu_part_online_school')}}
       </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@extends('dashboard.common')
@include('dashboard.lists')
