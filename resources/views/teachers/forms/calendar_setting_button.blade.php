@if($user->role==="teacher" || $user->role==="manager" || $user->role=='staff')
{{--
<a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}" role="button" class="btn btn-default btn-sm mr-1 mt-1">
  <i class="fa fa-file"></i><span class="btn-label">{{__('labels.details')}}</span>
</a>
--}}
@if($setting->status=="enabled")
<a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_add')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/to_calendar" role="button" class="btn btn-outline-primary btn-sm mr-1 mt-1">
  <i class="fa fa-calendar-plus"></i><span class="btn-label">{{__('labels.schedule_add')}}</span>
</a>
<a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.schedule_delete')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/delete_calendar" role="button" class="btn btn-outline-danger btn-sm mr-1 mt-1">
  <i class="fa fa-calendar-minus"></i><span class="btn-label">{{__('labels.schedule_delete')}}</span>
</a>
@endif
<a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.edit')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}/edit" role="button" class="btn btn-success btn-sm mr-1 mt-1">
  <i class="fa fa-edit"></i><span class="btn-label">{{__('labels.edit')}}</span>
</a>
<a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="{{__('labels.edit')}}" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}?action=delete" role="button" class="btn btn-danger btn-sm mr-1 mt-1">
  <i class="fa fa-trash"></i><span class="btn-label">{{__('labels.delete')}}</span>
</a>

@endif
