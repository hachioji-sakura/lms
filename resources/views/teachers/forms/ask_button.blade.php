@if($user->role==="teacher" || $user->role==="manager" )
{{-- @if($ask["status"]==="fix" && strtotime($ask["start_time"]) <= strtotime('now')) --}}
  @if($ask["status"]==="new")
  {{-- 講師予定確認済み --}}
  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="承認する" page_form="dialog" page_url="/asks/{{$ask["id"]}}/status_update/commit?origin={{$domain}}&item_id={{$teacher->id}}&page=ask" role="button" class="btn btn-success btn-sm">
    <i class="fa fa-check mr-1"></i>
    承認
  </a>
  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="差し戻し" page_form="dialog" page_url="/asks/{{$ask["id"]}}/status_update/cancel?origin={{$domain}}&item_id={{$teacher->id}}&page=ask" role="button" class="btn btn-secondary btn-sm">
    <i class="fa fa-times mr-1"></i>
    差戻
  </a>
  @else
  {{-- 参照のみ --}}
  <a href="javascript:void(0);" title="{{$ask["id"]}}" page_title="詳細" page_form="dialog" page_url="/asks/{{$ask["id"]}}" role="button" class="btn btn-outline-{{config('status_style')[$ask->status]}} btn-sm mr-1">
    <i class="fa fa-file-alt mr-1"></i>{{$ask["status_name"]}}
  </a>
  @endif
@endif
