<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
　  <div class="row">
      <div class="col-12 col-md-6 mt-2">
        <div class="form-group">
          <label for="title" class="w-100">
            イベント名(件名）
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" name="title" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item->title}}" placeholder="(変更前) {{$item->title}}">
          @else
           placeholder="">
          @endif
        </div>
      </div>
      <div class="col-12 col-md-6 mt-2">
        <div class="form-group">
          <label for="role" class="w-100">
            送信対象
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="user_role[]" class="form-control select2" width="100%" required="true" multiple="multiple">
            <option value="">{{__('labels.selectable')}}</option>
            @foreach(config('attribute.user_role') as $index=>$name)
                <option value="{{$index}}"
                @if($_edit===true && isset($item) && $item->has_tag('user_role', $index)===true)
                selected
                @endif
                >{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-12 col-md-6 mt-2">
        <div class="form-group">
          <label for="grade" class="w-100">
            送信対象条件（部門）
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          @foreach($attributes['lesson'] as $index => $name)
          <label class="mx-1">
            <input type="radio" value="{{ $index }}" name="lesson" class="icheck flat-green" required="true"
            @if($_edit===true && isset($item) && $item->has_tag('lesson', $index)===true)
            checked
            @endif
            onChange="lesson_checkbox_change(this)">{{$name}}
          </label>
          @endforeach
        </div>
      </div>
      <div class="col-12 col-md-6 mt-2">
        <div class="form-group">
          <label for="grade" class="w-100">
            送信対象条件（学年）/生徒のみ
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <select name="grade[]" class="form-control select2" width="100%" multiple="multiple">
            <option value="">{{__('labels.selectable')}}</option>
            @foreach($attributes['grade'] as $index => $name)
                <option value="{{$index}}"
                @if($_edit===true && isset($item) && $item->has_tag('grade', $index)===true)
                selected
                @endif
                >{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-12 mt-2">
        <div class="form-group">
          <label for="url" class="w-100">
            URL
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <input type="text" name="url" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item->url}}" placeholder="(変更前) {{$item->url}}">
          @else
           placeholder="">
          @endif
        </div>
        <h6 class="text-danger text-sm">
        </h6>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            内容
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" name="body" class="form-control"  maxlength=5000
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item->body}}" >{{$item->body}}</textarea>
          @else
            placeholder="5000文字まで" ></textarea>
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
              {{__('labels.add_button')}}
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
            {{__('labels.cancel_button')}}
          </button>
      </div>
    </div>
  </form>
</div>
