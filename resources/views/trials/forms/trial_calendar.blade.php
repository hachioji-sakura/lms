@if(count($item["calendars"])>0)
  @foreach($item["calendars"] as $calendar)
    <div class="row border-bottom">
      <div class="col-12 col-md-10 p-2">
        <span class="description-text mr-2">
          <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" >
          <i class="fa fa-clock mr-1"></i>
          {{$calendar["datetime"]}} /
          {{$calendar['lesson']}} /
          {{$calendar['course']}} /
          {{$calendar['place_floor_name']}}
          </a>
        </span>
        <small class="badge badge-{{config('status_style')[$calendar->status]}} mx-2">
          {{$calendar["status_name"]}}
        </small>
        <br>
        <span class="description-text mr-2">
        @foreach($calendar['teachers'] as $member)
          <a href='/teachers/{{$member->user->teacher->id}}'>
          <i class="fa fa-user-tie mr-1"></i>
          {{$member->user->teacher->name()}}
          </a>
        @endforeach
        </span>
        @foreach($calendar['subject'] as $subject)
        <span class="text-xs mx-2">
          <small class="badge badge-primary mt-1 mr-1">
            {{$subject}}
          </small>
        </span>
        @endforeach
      </div>
      @if($item->status=='new')
      <div class="col-12 col-md-2 my-1">
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendars/{{$calendar->id}}?action=delete&trial_id={{$item->id}}" page_title="削除" role="button" class="btn btn-sm btn-danger float-left mx-1 text-center">
          <i class="fa fa-trash"></i>
        </a>
      </div>
      @endif
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  授業予定が登録されていません
@endif
