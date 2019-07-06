@section('title')
  {{$domain_name}} {{__('labels.schedule_list')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            @if($list=="recent")
              {{__('labels.today_schedule_list')}}
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
            @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
              @slot("addon_button")
              <ul class="pagination pagination-sm m-0 float-left text-sm">
                <li class="page-item">
                  <a class="btn btn-info btn-sm" href="javascript:void(0);"  page_form="dialog" page_url="/calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
                    <i class="fa fa-plus"></i>
                    <span class="btn-label">{{__('labels.add')}}</span>
                  </a>
                </li>
              </ul>
              @endslot
            @endcomponent
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($calendars) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($calendars as $calendar)
            <li class="col-12 p-0" accesskey="" target="">
              <div class="row p-2
              @if($calendar->is_cancel_status()==true)
              calendar_rest
              @endif
                ">
                <div class="col-7 col-lg-4 col-md-4">
                  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" >
                    <i class="fa fa-calendar mx-1"></i>{{$calendar["dateweek"]}}
                    <br>
                    <i class="fa fa-clock mx-1"></i>{{$calendar["timezone"]}}
                    <br>
                    <i class="fa fa-map-marker mx-1"></i>{{$calendar["place_floor_name"]}}
                  </a>
                </div>
                <div class="col-5 col-lg-4 col-md-4">
                  @foreach($calendar->members as $member)
                    @if($member->user->details('students')->role==="student")
                      <a alt="student_name" href="/students/{{$member->user->details('students')->id}}" class="mr-2" target=_blank>
                        <i class="fa fa-user-graduate"></i>
                        {{$member->user->details('students')->name}}
                      </a>
                    @endif
                  @endforeach
                  <br>
                  @foreach($calendar['subject'] as $subject)
                  <span class="text-xs mx-2">
                    <small class="badge badge-primary mt-1 mr-1">
                      {{$subject}}
                    </small>
                  </span>
                  @endforeach
                </div>
                <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
                  @component('teachers.forms.calendar_button', ['teacher'=>$item, 'calendar' => $calendar, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
                  @endcomponent
                </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
          @endif
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
        @if(isset($filter['search_from_date']))
          value="{{$filter['search_from_date']}}"
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
        @if(isset($filter['search_to_date']))
          value="{{$filter['search_to_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="is_exchange" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
      @if(isset($filter['is_desc']) && $filter['is_desc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="is_exchange" class="w-100">
        {{__('labels.to_exchange')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_exchange" class="icheck flat-green"
      @if(isset($filter['is_exchange']) && $filter['is_exchange']==true)
        checked
      @endif
      >{{__('labels.to_exchange')}}
      </label>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
    <div class="w-100">
      <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
        @foreach(config('attribute.calendar_status') as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_status']) && in_array($index, $filter['search_status'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <label for="search_work" class="w-100">
      {{__('labels.work')}}
    </label>
    <div class="w-100">
      <select name="search_work[]" class="form-control select2" width=100% placeholder="検索作業" multiple="multiple" >
        @foreach($attributes['work'] as $index=>$name)
          <option value="{{$index}}"
          @if(isset($filter['search_work']) && in_array($index, $filter['search_work'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <label for="search_place" class="w-100">
      {{__('labels.place')}}
    </label>
    <div class="w-100">
      <select name="search_place[]" class="form-control select2" width=100% placeholder="検索場所" multiple="multiple" >
        @foreach($attributes['places'] as $place)
          @foreach($place->floors as $floor)
          <option value="{{$floor->id}}"
          @if(isset($filter['search_place']) && in_array($floor->id, $filter['search_place'])==true)
          selected
          @endif
          >{{$floor->name()}}</option>
          @endforeach
        @endforeach
      </select>
    </div>
  @endslot
@endcomponent
@endsection
