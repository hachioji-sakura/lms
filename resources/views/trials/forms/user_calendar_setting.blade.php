@if(count($item->calendar_settings)>0)
  @foreach($item->calendar_settings as $setting)
    <div class="row border-bottom">
      <div class="col-12 col-md-9 p-2 border-right">
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
        <br>
        <?php
        $t = $setting->user->details('teachers');
        $t_id = 0;
        if(isset($t)) $t_id = $t->id;
        ?>
        <a href="/teachers/{{$t_id}}/schedule?list=history&user_calendar_setting_id={{$setting->id}}" class="text-sm">
          {{__('labels.regist_schedule_count', ['count' => $setting['calendar_count']])}} /    {{__('labels.last_regist_date')}}:
          @isset($setting['last_schedule'])
          {{$setting['last_schedule']['date']}}
          @endisset
        </a>
      </div>
      <div class="col-12 col-md-3 my-1">

        @if($setting->status=='new' || $setting->status=='dummy')
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}/edit" page_title="{{__('labels.edit')}}" class="mr-1 underline text-sm">
          <i class="fa fa-edit"></i>{{__('labels.edit')}}
        </a>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}?action=delete&trial_id={{$item->id}}" page_title="{{__('labels.delete')}}"  class="mr-1 underline text-sm">
          <i class="fa fa-trash"></i>{{__('labels.delete')}}
        </a>
        @endif
        @if($setting->status=='fix')
        <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_add')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/to_calendar" role="button" class="btn btn-outline-success btn-sm ml-1">
          <i class="fa fa-calendar-plus"></i>
        </a>
        <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/delete_calendar" role="button" class="btn btn-outline-danger btn-sm  ml-1">
          <i class="fa fa-calendar-minus"></i>
        </a>
        @elseif($setting->status=='new')
        <br>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}/status_update/remind" page_title="{{__('labels.schedule_remind')}}" role="button" class="btn btn-sm btn-warning ml-1">
          <i class="fa fa-envelope"></i>{{__('labels.schedule_remind')}}
        </a>
        @elseif($setting->status=='dummy')
        <br>
        <a href="javascript:void(0);" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}" page_title="{{__('labels.dummy_release')}}" role="button" class="btn btn-sm btn-primary ml-1">
          <i class="fa fa-unlock-alt"></i>{{__('labels.dummy_release')}}
        </a>
        @endif
      </div>
    </div>
  @endforeach
@else
  <i class="fa fa-exclamation-triangle mr-1"></i>
  通常授業設定が登録されていません
@endif
