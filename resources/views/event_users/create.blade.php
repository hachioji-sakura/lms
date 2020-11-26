<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  <input type="hidden" name="event_id" value="{{$event->id}}" / >
　  <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="role" class="w-100">
            追加する送信対象を選択してください
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="user_id[]" class="form-control select2" width="100%" required="true" multiple="multiple">
            <option value="">{{__('labels.selectable')}}</option>
            @foreach($event->get_event_user() as $target)
              @if(!$event->has_user($target->user_id))
                <option value="{{$target->user_id}}"
                >{{$target->name()}}</option>
              @endif
            @endforeach
          </select>
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
