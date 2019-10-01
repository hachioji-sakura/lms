@section('title')
  {{$domain_name}}授業スケジュール
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content mb-2">
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
            @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
              @slot("addon_button")
              @endslot
            @endcomponent
          </div>
          </h3>
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
                <div class="col-5 col-lg-4 col-md-4">
                  <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?student_id={{$item->id}}" >
                  <i class="fa fa-calendar mx-1"></i>{{$calendar["dateweek"]}}
                  <small title="{{$calendar["id"]}}" class="badge badge-{{config('status_style')[$calendar['status']]}} mt-1 mx-1">{{$calendar["status_name"]}}</small>
                  <br>
                  <i class="fa fa-clock mx-1"></i>{{$calendar["timezone"]}}
                  <br>
                  <i class="fa fa-map-marker mx-1"></i>{{$calendar["place_floor_name"]}}
                  </a>
                </div>
                <div class="col-7 col-lg-4 col-md-4">
                  <i class="fa fa-user-tie mr-2"></i>
                  {{$calendar['teacher_name']}}
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
                  <a href="javascript:void(0);" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?student_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1 w-100">
                    <i class="fa fa-file-alt mr-1"></i>詳細
                  </a>
                  <br>
                  {{--
                    TODO　将来的に事務のみ代理連絡可能にする
                  @if($user->role!=="teacher" && $calendar->get_member($item->user_id)->status==="fix")
                  --}}
                  @if($calendar->get_member($item->user_id)->status==="fix" && $calendar["is_passed"]==false)
                  <a href="javascript:void(0);" page_title="休み連絡" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest?student_id={{$item->id}}" role="button" class="btn btn-danger btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="fix") disabled @endif>
                    <i class="fa fa-minus-circle mr-1"></i>休み連絡する
                    @if($user->role==="manager" || $user->role==="teacher")
                    (代理連絡）
                    @endif
                  </a>
                  @elseif($calendar->get_member($item->user_id)->status==="confirm")
                  <a href="javascript:void(0);" page_title="予定確認" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/fix?student_id={{$item->id}}" role="button" class="btn btn-primary btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="rest") disabled @endif>
                    <i class="fa fa-check mr-1"></i>予定確認
                    @if($user->role==="manager" || $user->role==="teacher")
                    (代理連絡）
                    @endif
                  </a>
                  @elseif($calendar->get_member($item->user_id)->status==="rest" && strtotime($calendar["start_time"]) > strtotime('now'))
                  <a href="javascript:void(0);" page_title="休み取り消し" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}/status_update/rest_cancel?student_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mt-1 mr-1 w-100" @if($calendar["status"]!=="rest") disabled @endif>
                    <i class="fa fa-minus-circle mr-1"></i>休み取り消し連絡
                    @if($user->role==="manager" || $user->role==="teacher")
                    (代理連絡）
                    @endif
                  </a>
                  @endif
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
      <!-- /.card -->
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
  <div class="col-12 col-md-4 mb-2">
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
  <div class="col-12 mb-2">
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
  @endslot
@endcomponent
@endsection
