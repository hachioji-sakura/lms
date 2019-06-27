@if(count($item["calendars"])>0)
  @foreach($item["calendars"] as $calendar)
    <div class="row border-bottom">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">概要</h5>
          <span class="description-text mr-2">
            <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}?action=delete" >
            <i class="fa fa-clock mr-1"></i>
            {{$calendar["datetime"]}}
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
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$calendar['lesson']}}
            </small>
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-success mt-1 mr-1">
              {{$calendar['place_floor_name']}}
            </small>
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$calendar['course']}}
            </small>
          </span>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="description-block">
          <h5 class="description-header">科目</h5>
          <span class="description-text">
            @foreach($calendar['subject'] as $subject)
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$subject}}
              </small>
            </span>
            @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-3 border-left">
        <div class="description-block">
          <h5 class="description-header">操作</h5>
          <span class="description-text">
            <div class="col-12 my-1">
              <a href="/{{$domain}}/{{$item->id}}/to_calendar_setting?calendar_id={{$calendar->id}}" role="button" class="btn btn-block btn-info">
                <i class="fa fa-cogs mr-1"></i>
                通常授業登録
              </a>
            </div>
          </span>
        </div>
      </div>
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  授業予定が登録されていません
@endif
