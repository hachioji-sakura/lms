@if(count($item->calendar_settings)>0)
  @foreach($item->calendar_settings as $setting)
    <div class="row border-bottom">
      <div class="col-12 col-md-10 p-2">
          <span class="description-text mr-2">
            <a href="javascript:void(0);" title="{{$setting->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}" >
              <i class="fa fa-clock mr-1"></i>
              {{$setting->lesson_week()}}/
              {{$setting->timezone()}} /
              {{$setting->lesson()}} /
              {{$setting->course()}} /
              {{$setting['place_floor_name']}}
            </a>
          </span>
          <small class="badge badge-{{config('status_style')[$setting->status]}} mx-2">
            {{$setting["status_name"]}}
          </small>
          <br>
          <span class="description-text mr-2">
          @foreach($setting['teachers'] as $member)
            <a href='/teachers/{{$member->user->teacher->id}}'>
            <i class="fa fa-user-tie mr-1"></i>
            {{$member->user->teacher->name()}}
            </a>
          @endforeach
          </span>
          @foreach($setting->subject() as $subject)
          <span class="text-xs mx-2">
            <small class="badge badge-primary">
              {{$subject}}
            </small>
          </span>
          @endforeach
        </span>
      </div>
      <div class="col-12 col-md-2 my-1">
        @if($setting->status=='new')
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}/status_update/confirm" page_title="確認連絡" role="button" class="btn btn-sm btn-warning float-left mx-1 text-center">
          <i class="fa fa-envelope"></i>
        </a>
        @endif
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}/edit" page_title="編集" role="button" class="btn btn-sm btn-success float-left mx-1 text-center">
          <i class="fa fa-edit"></i>
        </a>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}?action=delete&trial_id={{$item->id}}" page_title="削除" role="button" class="btn btn-sm btn-danger float-left mx-1 text-center">
          <i class="fa fa-trash"></i>
        </a>
      </div>
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  通常授業設定が登録されていません
@endif
