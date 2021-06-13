<div class="row mt-2">

  <div class="col-6 text-left">
    <a href="javascript:void(0);" page_title="見積書送信" page_form="dialog" page_url="/lesson_requests/{{$item->id}}/estimate" class="mr-1 btn btn-sm btn-success ">
      <i class="fa fa-envelope"></i>
      見積書送信
    </a>
  </div>
  <div class="col-6 text-right">
  <a title="{{$item->id}}" href="javascript:void(0);" page_title="申し込み編集" page_form="dialog" page_url="/lesson_requests/{{$item->id}}" role="" class="mr-1 btn btn-sm btn-default ">
    <i class="fa fa-file"></i>
    {{__('labels.details')}}
  </a>
  <a href="/events/{{$event->id}}/schedules?lesson_request_id={{$item->id}}" class="mr-1 btn btn-sm btn-default ">
    <i class="fa fa-calendar"></i>
    候補予定一覧
  </a>
  @if($item->status=='new')
  <a title="{{$item->id}}" href="javascript:void(0);" page_title="申し込み編集" page_form="dialog" page_url="/lesson_requests/{{$item->id}}/edit" role="" class="mr-1 btn btn-sm btn-outline-success ">
    <i class="fa fa-edit"></i>
    {{__('labels.edit')}}
  </a>
  <a title="{{$item->id}}" href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$item['id']}}?action=delete"  role="" class="mr-1 btn btn-sm btn-outline-danger ">
    <i class="fa fa-trash mr-1"></i>{{__('labels.delete')}}
  </a>

  @endif
  </div>
</div>
