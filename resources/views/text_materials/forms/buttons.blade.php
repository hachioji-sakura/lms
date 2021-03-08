<div class="col-12">
<a href="{{$text_material->s3_url}}" target="_blank" role="button" class="btn btn-info btn-sm float-left mr-1">
  <i class="fa fa-cloud-download-alt"></i>
</a>
@if($text_material->is_publiced()==false)
<a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.share')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/shared?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn
  @if($text_material->shared_users()->count()>0)
  btn-warning
  @else
  btn-secondary
  @endif
  btn-sm float-left mr-1">
  <i class="fa fa-share-alt-square"></i>
</a>
@endif
@if($item->user_id == $text_material->target_user_id)
<a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.edit')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-right mr-1">
  <i class="fa fa-edit"></i>
</a>
<a href="javascript:void(0);" page_title="{{__('labels.text_materials')}}{{__('labels.delete')}}" page_form="dialog" page_url="/text_materials/{{$text_material->id}}?origin={{$domain}}&item_id={{$item->id}}&action=delete" role="button" class="btn btn-default btn-sm float-right mr-1">
  <i class="fa fa-trash"></i>
</a>
@endif
</div>
