@if(count($item["calendars"])>0)
  @foreach($item["calendars"] as $calendar)
    <div class="row border-bottom
    @if($calendar->is_cancel_status()==true)
    calendar_rest
    @endif
    ">
      <div class="col-12 col-md-9 p-2 border-right">
        <span class="description-text mr-2">
          <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" >
          <i class="fa fa-clock mr-1"></i>
          {{$calendar["datetime"]}} /
          @if($calendar->is_teaching()==true)
          {{$calendar['lesson']}} /
          {{$calendar['course']}} /
          @endif
          {{$calendar['place_floor_name']}}
          </a>
        </span>
        <br>
        <small class="badge badge-{{config('status_style')[$calendar->status]}} mx-2">
          {{$calendar["status_name"]}}
        </small>
        <span class="description-text mr-2">
        @foreach($calendar['teachers'] as $member)
          <a href='/teachers/{{$member->user->teacher->id}}'>
          <i class="fa fa-user-tie mr-1"></i>
          {{$member->user->teacher->name()}}
          </a>
        @endforeach
        </span>
        @if($calendar->is_teaching()==true)
          @foreach($calendar['subject'] as $subject)
          <span class="text-xs mx-2">
            <small class="badge badge-primary mt-1 mr-1">
              {{$subject}}
            </small>
          </span>
          @endforeach
        @else
        <small class="badge badge-warning mx-1 text-sm">
          <i class="fa fa-exclamation-triangle">{{$calendar->work_name}}</i>
        </small>
        @endif
        @if($calendar->is_online()==true)
        <small class="badge badge-info mx-1 text-sm">
          <i class="fa fa-globe">{{__('labels.online')}}</i>
        </small>
        @endif
      </div>
      @if($calendar->status=='dummy' || $calendar->status=='new' || $calendar->status=='confirm' || $calendar->status=='cancel')
      <div class="col-12 col-md-3 my-1">
        @if($calendar->status!='cancel')
        {{--
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/cancel?trial_id={{$item->id}}" page_title="{{__('labels.cancel')}}" class="mr-1 underline text-sm">
          <i class="fa fa-ban"></i>{{__('labels.cancel')}}
        </a>
        --}}
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}/edit?trial_id={{$item->id}}" page_title="{{__('labels.edit')}}" class="mr-1 underline text-sm">
          <i class="fa fa-edit"></i>{{__('labels.edit')}}
        </a>
        @endif
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}?action=delete&trial_id={{$item->id}}" page_title="{{__('labels.delete')}}" class="mr-1 underline text-sm">
          <i class="fa fa-trash"></i>{{__('labels.delete')}}
        </a>
        @if($calendar->status=='new')
        <br>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}/status_update/remind?trial_id={{$item->id}}" page_title="{{__('labels.schedule_remind')}}" class="mr-1 btn btn-sm btn-warning">
          <i class="fa fa-envelope"></i>{{__('labels.schedule_remind')}}
        </a>
        @elseif($calendar->status=='dummy')
        <br>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}" page_title="{{__('labels.schedule_remind')}}" class="mr-1 btn btn-sm btn-primary">
          <i class="fa fa-unlock-alt"></i>{{__('labels.dummy_release')}}
        </a>
        @endif
      </div>
      @endif
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  授業予定が登録されていません
@endif
