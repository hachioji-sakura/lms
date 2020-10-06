@if($user->role==="teacher" || $user->role==="manager" || $user->role=='staff')
  @if($setting->status=="fix" && $user->role==="manager")
  <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_add')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/to_calendar" role="button" class="btn btn-outline-success btn-sm mr-1 mt-1">
    <i class="fa fa-calendar-plus"></i><span class="btn-label">{{__('labels.schedule_add')}}</span>
  </a>
  <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/delete_calendar" role="button" class="btn btn-outline-danger btn-sm mr-1 mt-1">
    <i class="fa fa-calendar-minus"></i><span class="btn-label">{{__('labels.schedule_delete')}}</span>
  </a>
  @endif
  @if($setting->status=="new")
  <a title="{{$setting["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_fix')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/status_update/confirm" role="button" class="btn btn-warning btn-sm ml-1">
    <i class="fa fa-user-check mr-1"></i>
    {{__('labels.schedule_fix')}}
  </a>
  @elseif($setting->status=="confirm")
  {{-- 生徒へ再度通知連絡 --}}
  <a title="{{$setting["id"]}}" href="javascript:void(0);" page_title="{{__('labels.schedule_remind')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/status_update/remind" role="button" class="btn btn-warning btn-sm ml-1">
    <i class="fa fa-envelope mr-1"></i>
    <span class="ml-1 btn-label">
    {{__('labels.schedule_remind')}}
  </a>
  @elseif($setting->status=="dummy" && $user->role==="manager")
  {{-- ダミー解除 --}}
  <a title="{{$setting["id"]}}" href="javascript:void(0);" page_title="{{__('labels.dummy_release')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}" role="button" class="btn btn-primary btn-sm ml-1">
    <i class="fa fa-unlock-alt mr-1"></i>
    <span class="ml-1 btn-label">
    {{__('labels.dummy_release')}}
  </a>
  @endif

  <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.edit')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/edit" role="button" class="btn btn-success btn-sm mr-1 mt-1">
    <i class="fa fa-edit"></i><span class="btn-label">{{__('labels.edit')}}</span>
  </a>
  @if($user->role==="manager")
  <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.edit')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}?action=delete" role="button" class="btn btn-danger btn-sm mr-1 mt-1">
    <i class="fa fa-trash"></i><span class="btn-label">{{__('labels.delete')}}</span>
  </a>
  @endif
@endif
