<a href="trials/{{$item->id}}" role="" class="mr-1 underline">
  <i class="fa fa-file"></i>
  {{__('labels.details')}}
</a>
<a title="{{$item["id"]}}" href="javascript:void(0);" page_title="体験申し込み編集" page_form="dialog" page_url="/trials/{{$item["id"]}}/edit" role="" class="mr-1 underline">
  <i class="fa fa-edit"></i>
  {{__('labels.edit')}}
</a>
<a title="{{$item["id"]}}" href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}/cancel"  role="" class="mr-1 underline">
  <i class="fa fa-times mr-1"></i>{{__('labels.delete')}}
</a>
<br>
@if($item->is_trial_lesson_complete()==false)
<a href="trials/{{$item->id}}/to_calendar" role="button" class="btn btn-info btn-sm mt-1">
  <i class="fa fa-plus mr-1"></i>
  体験授業登録
</a>
<a class="btn btn-sm btn-flat btn-success mt-1" role="button"  href="javascript:void(0);" page_title="入会希望を受け取る連絡を出す" page_form="dialog" page_url="/trials/{{$item["id"]}}/ask_hope_to_join">
  <i class="fa fa-envelope mr-1"></i>
  入会希望に関するご連絡
</a>
@endif
@if($item->is_trial_lesson_complete()==true)
<a href="trials/{{$item->id}}/to_calendar_setting" role="button" class="btn btn-info btn-sm mt-1">
  <i class="fa fa-plus mr-1"></i>
  通常授業登録
</a>
@endif
@if($item->status=='entry_hope' || $item->status=='entry_guidanced')
<a class="btn btn-sm btn-flat btn-success mt-1" role="button"  href="javascript:void(0);" page_title="契約申込に関する連絡" page_form="dialog" page_url="/trials/{{$item["id"]}}/admission">
  <i class="fa fa-envelope mr-1"></i>契約申込に関する連絡
</a>
@endif
