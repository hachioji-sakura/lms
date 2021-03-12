<div id="{{$domain}}_shared">
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}/shared" >
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            {{__('labels.share')}}
            {{__('labels.teachers')}}
          </label>
          <select name="shared_user_ids[]" class="form-control select2"  required="true" width=100%  multiple="multiple">
            <option value="">{{__('labels.selectable')}}</option>
            @foreach($teachers as $teacher)
               <option
               value="{{ $teacher->user_id }}"
               {{$item->shared_users->count() >0 && $item->shared_users->contains($teacher->user_id)  ? "selected" : "" }}
               >{{$teacher->name()}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_shared">
            {{__('labels.update_button')}}
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
