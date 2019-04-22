@if(count($item->user_calendar_settings)>0)
  @foreach($item->user_calendar_settings as $setting)
    <div class="row border-bottom">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">概要</h5>
          <span class="description-text mr-2">
            <a href="javascript:void(0);" title="{{$setting->id}}" page_title="詳細" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}" >
              <i class="fa fa-clock mr-1"></i>
              {{$setting["lesson_week_name"]}}曜日/
              {{$setting->timezone()}}
            </a>
          </span>

          <br>
          <span class="description-text mr-2">
          @foreach($setting['teachers'] as $member)
            <a href='/teachers/{{$member->user->teacher->id}}'>
            <i class="fa fa-user-tie mr-1"></i>
            {{$member->user->teacher->name()}}
            </a>
          @endforeach
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$setting->lesson()}}
            </small>
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-success mt-1 mr-1">
              {{$setting->place()}}
            </small>
          </span>
          <span class="text-xs mx-2">
            <small class="badge badge-info mt-1 mr-1">
              {{$setting->course()}}
            </small>
          </span>
        </div>
      </div>
      <div class="col-sm-4 border-right">
        <div class="description-block">
          <h5 class="description-header">科目</h5>
          <span class="description-text">
            @foreach($setting->subject() as $subject)
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$subject}}
              </small>
            </span>
            @endforeach
          </span>
        </div>
      </div>
      <div class="col-sm-2">
        <div class="description-block">
          <h5 class="description-header">操作</h5>
          <span class="description-text">
            <div class="col-12 my-1">
              <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}/edit" page_title="編集" role="button" class="btn btn-sm btn-success float-left mx-1 text-center">
                <i class="fa fa-edit"></i>
              </a>
              <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}?action=delete" page_title="削除" role="button" class="btn btn-sm btn-danger float-left mx-1 text-center">
                <i class="fa fa-trash"></i>
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
