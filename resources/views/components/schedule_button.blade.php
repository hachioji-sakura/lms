@if($calendar->status==="fix")
@else
@if(date('Ymd', strtotime($calendar->start_time)) === date('Ymd')
@endif
<a title="" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$calendar->id}}" role="button" class="btn btn-success btn-sm w-100 mt-1">
  <i class="fa fa-check-circle mr-1"></i>
  {{$calendar->status_name}}
</a>
@endif
