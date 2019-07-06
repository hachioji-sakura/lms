<div id="{{$domain}}_create">
@if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}?key={{$select_key}}">
  @endif
  @csrf

    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_key" class="w-100">
            属性キー
          </label>
          {{$select_key_name}}({{$select_key}})
          <input type="hidden" id="attribute_key" name="attribute_key" value="{{$select_key}}">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_value" class="w-100">
            属性値
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @if(isset($_edit) && $_edit==true)
          <input type="hidden" id="attribute_value_org" name="attribute_value_org" value="{{$item['attribute_value']}}">
          <input type="text" id="attribute_value" name="attribute_value" value="{{$item['attribute_value']}}" class="form-control" placeholder="(変更前) {{$item['attribute_value']}}" required="true" >
          @else
          <input type="text" id="attribute_value" name="attribute_value" class="form-control" placeholder="例:1" required="true" >
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="attribute_name" class="w-100">
            属性名
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @if(isset($_edit) && $_edit==true)
          <input type="text" id="attribute_name" name="attribute_name" value="{{$item['attribute_name']}}" class="form-control" placeholder="(変更前) {{$item['attribute_name']}}" required="true" >
          @else
          <input type="text" id="attribute_name" name="attribute_name" class="form-control" placeholder="例：男性" required="true">
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
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
      <div class="col-12 col-lg-6 col-md-6 mb-1">
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
      <div class="col-12 col-lg-6 col-md-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
