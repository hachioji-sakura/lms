<a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="詳細" page_form="dialog" page_url="/asks/{{$ask->id}}" role="button" class="btn btn-default btn-sm">
  <i class="fa fa-file mr-1"></i>
  詳細
</a>

@if($user->role==="teacher" || $user->role==="manager" )
  @if($ask["status"]==="new" && ($ask["charge_user_id"]==$item->user_id || $domain=='managers'))
  {{-- 講師予定確認済み --}}
  {{--
  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="再送する" page_form="dialog" page_url="/asks/{{$ask["id"]}}/status_update/remind?origin={{$domain}}&item_id={{$item->id}}&page=ask" role="button" class="btn btn-default btn-sm">
    <i class="fa fa-envelope mr-1"></i>
    再送
  </a>
  --}}
  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="承認する" page_form="dialog" page_url="/asks/{{$ask["id"]}}/status_update/commit?origin={{$domain}}&item_id={{$item->id}}&page=ask" role="button" class="btn btn-primary btn-sm">
    <i class="fa fa-check mr-1"></i>
    承認
  </a>
  <a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="差戻し" page_form="dialog" page_url="/asks/{{$ask["id"]}}/status_update/cancel?origin={{$domain}}&item_id={{$item->id}}&page=ask" role="button" class="btn btn-secondary btn-sm">
    <i class="fa fa-times mr-1"></i>
    差戻
  </a>
  @else
  {{-- 参照のみ --}}
  {{--
  <a href="javascript:void(0);" title="{{$ask["id"]}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/asks/{{$ask["id"]}}" role="button" class="btn btn-outline-secondary btn-sm mr-1">
    <i class="fa fa-file-alt mr-1"></i>詳細
  </a>
  --}}
  @endif
@endif
@if($user->id == $ask->create_user_id && $ask['type'] != 'teacher_change')
<a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼内容編集" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/ask/{{$ask->id}}/edit" role="button" class="btn btn-success btn-sm">
  <i class="fa fa-edit mr-1"></i>
  編集
</a>
<a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="依頼内容削除" page_form="dialog" page_url="/asks/{{$ask->id}}?action=delete" role="button" class="btn btn-danger btn-sm">
  <i class="fa fa-trash mr-1"></i>
  削除
</a>
@endif

@if($user->role==="manager" )
@if($ask->type=='unsubscribe' && $ask->status=='new')
<a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="退会予定日変更" page_form="dialog" page_url="/asks/{{$ask->id}}/edit_date" role="button" class="btn btn-success btn-sm">
  <i class="fa fa-edit mr-1"></i>
  退会予定日変更
</a>
@elseif($ask->type=='recess' && $ask->status=='new')
<a title="{{$ask["id"]}}" href="javascript:void(0);" page_title="退会予定日変更" page_form="dialog" page_url="/asks/{{$ask->id}}/edit_date" role="button" class="btn btn-success btn-sm">
  <i class="fa fa-edit mr-1"></i>
  休会予定期間変更
</a>
@endif
@endif
