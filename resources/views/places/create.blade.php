<?php
if(!isset($item)) $item = null;
?>
<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}" enctype="multipart/form-data">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-12">
        <label>
          {{__('labels.status')}}
        </label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <div class="input-group">
          @foreach(config('attribute.place_status') as $key => $value)
          <div class="form-check">
            <label class="form-check-label" for="type_{{$key}}">
              <input class="frm-check-input icheck flat-green" type="radio" name="status" id="type_{{$key}}" value="{{$key}}" required="true" {{$_edit && $item->status == $key ? 'checked': ''}}>
              {{$value}}
            </label>
          </div>
          @endforeach
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="name" class="w-100">
            名称
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" name="name" class="form-control" required="true" maxlength=50 inputtype="zenkaku"
          @if(isset($_edit) && $_edit==true)
           value="{{$item->name}}" placeholder="(変更前) {{$item->name}}">
          @else
           placeholder="50文字まで">
          @endif
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="name_en" class="w-100">
            名称(英語）
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" name="name_en" class="form-control" required="true" maxlength=50 inputtype="hankaku"
          @if(isset($_edit) && $_edit==true)
           value="{{$item->name_en}}" placeholder="(変更前) {{$item->name_en}}">
          @else
           placeholder="50文字まで">
          @endif
        </div>
      </div>
      @component('students.forms.phoneno', ['_edit'=>$_edit, 'item' => $item, 'attributes' => $attributes]) @endcomponent
      @component('students.forms.address', ['_edit'=>$_edit, 'item' => $item, 'attributes' => $attributes]) @endcomponent
      <div class="col-3">
        <div class="form-group">
          <label for="sort_no" class="w-100">
            並び順
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @if(isset($_edit) && $_edit==true)
          <input type="text" id="sort_no" name="sort_no" value="{{$item['sort_no']}}" class="form-control" placeholder="(変更前) {{$item['sort_no']}}" required="true" >
          @else
          <input type="text" id="sort_no" name="sort_no" class="form-control" placeholder="例：1" required="true" inputtype="numeric">
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              登録する
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
<script>
$(function(){
  base.pageSettinged('{{$domain}}_create', null);
});
</script>
