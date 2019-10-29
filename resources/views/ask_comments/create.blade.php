<div id="{{$domain}}_create">
@if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
@else
  <form id="edit" method="POST" action="/{{$domain}}">
@endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="ask_id" value="{{$ask_id}}" / >
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            内容
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <textarea type="text" id="body" name="body" class="form-control" placeholder="" required="true">@if(isset($_edit) && $_edit==true){{$item->body}}@endif</textarea>
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
