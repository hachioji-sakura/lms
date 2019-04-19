<div class="card card-widget mb-2">
  <div class="card-header">
    <i class="fa fa-envelope-open-text mr-1"></i>体験授業予定
    <a role="button" class="btn btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar">
      <i class="fa fa-plus mr-1"></i>体験授業予定を設定する
    </a>
  </div>
  <div class="card-footer">
@if(count($item["calendars"])>0)
  @foreach($item["calendars"] as $calendar)
    <div class="row">
      <div class="col-sm-3 border-right">
        <div class="description-block">
          <h5 class="description-header">対応状況</h5>
          <span class="description-text">
            <small class="badge badge-{{config('status_style')[$calendar->status]}} mx-2">
              {{$calendar["status_name"]}}
            </small>
          </span>
        </div>
      </div>
      <div class="col-sm-4 border-right">
        <div class="description-block">
          <h5 class="description-header">予定</h5>
          <span class="description-text">
            <a href="javascript:void(0);" title="{{$calendar["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendars/{{$calendar["id"]}}" >
            <i class="fa fa-clock mr-1"></i>
            {{$calendar["datetime"]}}
            </a>
          </span>
        </div>
      </div>
      <div class="col-sm-2 border-right">
        <div class="description-block">
          <h5 class="description-header">講師</h5>
          <span class="description-text">
          @foreach($calendar['teachers'] as $teacher)
            <a href='/teachers/{{$teacher['id']}}'>
            <i class="fa fa-user-tie mr-1"></i>
            {{$teacher->name()}}
            </a>
          @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-3 ">
        <div class="description-block">
          <h5 class="description-header">内容</h5>
          <span class="description-text">
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$calendar['lesson']}}
              </small>
            </span>
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$calendar['course']}}
              </small>
            </span>
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
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  授業予定が登録されていません
@endif
  </div>
</div>
