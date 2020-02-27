@if(count($calendars) > 0)
<ul class="mailbox-attachments clearfix row">
  @foreach($calendars as $calendar)
  <li class="col-12 p-0" accesskey="" target="">
    <div class="row p-2">
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
      <div class="col-12 text-sm mt-1">
        <small class="badge badge-warning mt-1 mr-1">
          {{__('labels.exchange_limit_date')}} {{$calendar->exchange_limit_date()}}
        </small>
        <small class="badge badge-danger mt-1 mr-1">
          {{__('labels.exchange_remaining_time', ["time" => $calendar->get_exchange_remaining_time()])}}
        </small>
        <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.exchange_add')}}" page_form="dialog" page_url="/calendars/create?exchanged_calendar_id={{$calendar["id"]}}" role="button" class="btn btn-primary btn-sm ml-1 float-right">
          <i class="fa fa-exchange-alt"></i>
          <span class="ml-1 btn-label">
            {{__('labels.exchange_add')}}
          </span>
        </a>
        <a href="javascript:void(0);" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?student_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-right mr-1">
          <i class="fa fa-file-alt mr-1"></i>詳細
        </a>
      </div>
  </li>
  @endforeach
</ul>
@else
<div class="alert">
  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
</div>
@endif
