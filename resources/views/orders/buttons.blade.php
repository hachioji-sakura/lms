@if($row->status == "new")
<a href="javascript:void(0);" page_title="{{(__('messages.status_update_confirm',["status"  => config('attribute.order_status')['fix']]))}}" page_form="dialog" page_url="/orders/{{$row['id']}}/status_update/fix" role="button" class="btn btn-{{config('status_style')['fix']}} btn-sm float-left mr-1 my-1">
  <i class="fa fa-{{config('attribute.order_status_icon')['fix']}}"></i>
  {{__('labels.approval')}}
</a>

<a href="javascript:void(0);" page_title="{{__('labels.orders')}}{{__('labels.edit')}}" page_form="dialog" page_url="/orders/{{$row['id']}}/edit" role="button" class="btn btn-success btn-sm float-left mr-1 my-1">
  <i class="fa fa-edit"></i>
</a>
<a href="javascript:void(0);" page_title="{{__('messages.delete_confirm')}}" page_form="dialog" page_url="/orders/{{$row['id']}}/delete" role="button" class="btn btn-danger btn-sm float-left mr-1 my-1">
  <i class="fa fa-trash"></i>
</a>
@endif
@if($row->status == "fix")
<a href="javascript:void(0);" page_title="{{(__('messages.status_update_confirm',["status"  => config('attribute.order_status')['ordered']]))}}" page_form="dialog" page_url="/orders/{{$row['id']}}/status_update/ordered" role="button" class="btn btn-{{config('status_style')['ordered']}} btn-sm float-left mr-1 my-1">
  <i class="fa fa-{{config('attribute.order_status_icon')['ordered']}}"></i>
  {{__('labels.ordered')}}
</a>
@endif
@if($row->status == "ordered")
<a href="javascript:void(0);" page_title="{{(__('messages.status_update_confirm',["status"  => config('attribute.order_status')['recieved']]))}}" page_form="dialog" page_url="/orders/{{$row['id']}}/status_update/recieved" role="button" class="btn btn-{{config('status_style')['recieved']}} btn-sm float-left mr-1 my-1">
  <i class="fa fa-{{config('attribute.order_status_icon')['recieved']}}"></i>
  {{__('labels.recieved')}}
</a>
@endif
@if($row->status == "recieved")
<a href="javascript:void(0);" page_title="{{(__('messages.status_update_confirm',["status"  => config('attribute.order_status')['complete']]))}}" page_form="dialog" page_url="/orders/{{$row['id']}}/status_update/complete" role="button" class="btn btn-{{config('status_style')['complete']}} btn-sm float-left mr-1 my-1">
  <i class="fa fa-{{config('attribute.order_status_icon')['compolete']}}"></i>
  {{__('labels.complete')}}
</a>
@endif
@if($row->status == "new" || $row->status == "fix")
<a href="javascript:void(0);" page_title="{{(__('messages.status_update_confirm',["status"  => config('attribute.order_status')['cancel']]))}}" page_form="dialog" page_url="/orders/{{$row['id']}}/status_update/cancel" role="button" class="btn btn-{{config('status_style')['cancel']}} btn-sm float-left mr-1 my-1">
  <i class="fa fa-{{config('attribute.order_status_icon')['cancel']}}"></i>
  {{__('labels.cancel')}}
</a>
@endif
