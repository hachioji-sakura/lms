@if(count($calendars) > 0)
<ul class="mailbox-attachments clearfix row">
  @foreach($calendars as $calendar)
  <li class="col-12 p-0" accesskey="" target="">
    <div class="row p-2">
      <div class="col-7 col-lg-4 col-md-4">
        <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" >
          <i class="fa fa-calendar mx-1"></i>{{$calendar["dateweek"]}}
          <br>
          <i class="fa fa-clock mx-1"></i>{{$calendar["timezone"]}}
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
      <div class="col-12 text-sm mt-1">
        <small class="badge badge-warning mt-1 mr-1">
          期限 {{$calendar->exchange_limit_date()}}
        </small>
        <small class="badge badge-danger mt-1 mr-1">
          残り{{$calendar->get_exchange_remaining_time()}}分
        </small>
      </div>
      <div class="col-12 text-sm mt-1">
        <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.exchange_add')}}" page_form="dialog" page_url="/calendars/create?exchanged_calendar_id={{$calendar["id"]}}" role="button" class="btn btn-info btn-sm ml-1 float-right">
          <i class="fa fa-exchange-alt"></i>
          <span class="ml-1 ">
            {{__('labels.exchange_add')}}
          </span>
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
