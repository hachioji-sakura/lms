<div class="text-right">
<a title="{{$item->id}}" href="javascript:void(0);" page_title="申し込み編集" page_form="dialog" page_url="/lesson_requests/{{$item->id}}" role="" class="mr-1 underline" class="mr-1 underline">
  <i class="fa fa-file"></i>
  {{__('labels.details')}}
</a>
@if($item->status=='new')
<a title="{{$item->id}}" href="javascript:void(0);" page_title="申し込み編集" page_form="dialog" page_url="/lesson_requests/{{$item->id}}/edit" role="" class="mr-1 underline">
  <i class="fa fa-edit"></i>
  {{__('labels.edit')}}
</a>
<a title="{{$item->id}}" href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}/cancel"  role="" class="mr-1 underline">
  <i class="fa fa-times mr-1"></i>{{__('labels.delete')}}
</a>
{{--
<br>
@if($item->is_request_lesson_complete()==false)
<a href="lesson_requests/{{$item->id}}/to_calendar" role="button" class="btn btn-info btn-sm mt-1">
  <i class="fa fa-plus mr-1"></i>
  授業登録
</a>
@endif
--}}
@endif
</div>
