@section('title')
  {{$domain_name}} {{__('labels.schedule_list')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('schedule_list_pager')
@component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
  @slot("addon_button")
  <ul class="pagination pagination-sm m-0 float-left text-sm">
    <li class="page-item ml-1">
      <a class="btn btn-info btn-sm" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
        <i class="fa fa-plus"></i>
        <span class="btn-label">{{__('labels.add')}}</span>
      </a>
    </li>
  </ul>
  @endslot
@endcomponent
@endsection

@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            @if($list=="today")
              {{__('labels.today_schedule_list')}}
            @elseif($list=="month")
              {{__('labels.month_schedule_list')}}
            @elseif($list=="confirm")
              {{__('labels.adjust_schedule_list')}}
            @elseif($list=="confirm")
              {{__('labels.adjust_schedule_list')}}
            @elseif($list=="cancel")
              {{__('labels.rest_schedule_list')}}
            @elseif($list=="exchange")
              {{__('labels.exchange_schedule_list')}}
            @elseif($list=="history")
              {{__('labels.schedule_history')}}
            @else
              {{__('labels.schedule_list')}}
            @endif
          </h3>
          <div class="card-title text-sm">
            @yield('schedule_list_pager')
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if($list=="exchange")
            @component('teachers.forms.exchange_schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'teacher' => $item]) @endcomponent
          @else
            @component('teachers.forms.schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'teacher' => $item]) @endcomponent
          @endif
        </div>
        <div class="card-header">
          <div class="card-title text-sm">
            @yield('schedule_list_pager')
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

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
      <label for="is_exchange" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
      @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  @component('calendars.filter', ['domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'item' => $item, 'filter'=>$filter])
  @endcomponent
  @endslot
@endcomponent
@endsection
