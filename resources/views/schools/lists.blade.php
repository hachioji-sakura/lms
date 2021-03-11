@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="javascript:void(0);" page_title="{{__('labels.school_page_high_school')}}{{__('labels.add')}}" page_form="dialog" page_url="/{{$domain}}/create_form" class="nav-link">
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

@section('contents')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">{{ $school_view_entity->pageTitle() }}</h3>
    <div class="card-title text-sm">
      <div class="card-title text-sm">
        {{$paginator->appends(Request::query())->links('components.paginate', ['is_not_filter_button' => true])}}
      </div>
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
    </div>
  </div>
  <div class="card-body table-responsive p-0">

    @if(count($high_school_entities) > 0)
    <table class="table table-hover">
      <tbody>
        <tr>
            <th>{{ $school_view_entity->localizeName('name') }}</th>
            <th>{{ $school_view_entity->localizeName('address') }}</th>
            <th style="width: 40%;">{{ $school_view_entity->localizeName('department_names') }}</th>
            <th>{{ $school_view_entity->localizeName('process') }}</th>
            <th style="width: 160px;">{{ $school_view_entity->localizeName('control') }}</th>
        </tr>
        @foreach($paginator as $high_school_entity)
          <tr>
              <td>
                <a href="{{ $high_school_entity->url() }}" target="_blank" rel="noopener noreferrer">{!! $high_school_entity->name() !!}</a>
              </td>
              <td>
                {!! $high_school_entity->address()  !!}
              </td>
              <td>
                {!! $high_school_entity->departmentNames()  !!}
              </td>
              <td>
                {!! $high_school_entity->process() !!}
              </td>
              <td>
                @include('schools.component.button.detail_button',['page_title' => $school_view_entity->pageTitle(), 'page_url' => $domain . '/detail?id='.$high_school_entity->highSchoolId()])
                @include('schools.component.button.edit_button',['page_title' => $school_view_entity->pageTitle(), 'page_url' => $domain . '/edit?id='.$high_school_entity->highSchoolId()])
                @include('schools.component.button.delete_button',['page_title' => $school_view_entity->pageTitle(), 'page_url' => $domain . '/delete_confirmation?id='.$high_school_entity->highSchoolId()])
              </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @else
    <div class="alert">
      <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
    </div>
    @endif
  </div>
</div>
@endsection

@extends('dashboard.common')
