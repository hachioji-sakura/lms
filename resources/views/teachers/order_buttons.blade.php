@if($row->status == "new")
<a href="javascript:void(0);" page_title="{{__('labels.orders')}}{{__('labels.edit')}}" page_form="dialog" page_url="/orders/{{$row['id']}}/edit" role="button" class="btn btn-success btn-sm float-left mr-1 my-1">
  <i class="fa fa-edit"></i>
</a>
<a href="javascript:void(0);" page_title="{{__('messages.delete_confirm')}}" page_form="dialog" page_url="/orders/{{$row['id']}}/delete" role="button" class="btn btn-danger btn-sm float-left mr-1 my-1">
  <i class="fa fa-trash"></i>
</a>
@endif
