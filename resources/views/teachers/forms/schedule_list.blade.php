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
          <small title="{{$calendar["id"]}}" class="badge badge-{{config('status_style')[$calendar['status']]}} mt-1 mx-1">{{$calendar["status_name"]}}</small>
          @if($calendar->is_trial()==true)
          <small class="badge badge-danger mt-1 mx-1">{{__('labels.trial')}}</small>
          @endif
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
        @if($calendar->is_teaching()==true)
          @foreach($calendar['subject'] as $subject)
          <span class="text-xs mx-2">
            <small class="badge badge-primary mt-1 mr-1">
              {{$subject}}
            </small>
          </span>
          @endforeach
        @else
        <span class="text-xs mx-2">
          <small class="badge badge-primary mt-1 mr-1">
            {{$calendar["work_name"]}}
          </small>
        </span>
        @endif
      </div>
      <div class="col-12 col-lg-4 col-md-4 text-sm mt-1">
        @component('teachers.forms.calendar_button', ['teacher'=>$teacher, 'calendar' => $calendar, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
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
