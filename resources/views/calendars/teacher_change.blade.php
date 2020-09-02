@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @endslot
  @slot('forms')
  <div id="_form">
  <form method="POST" action="/asks">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    {{--注意
    <input type="hidden" name="parent_ask_id" value="{{$ask->id}}">
    --}}
    <input type="hidden" name="type" value="teacher_change">
    <input type="hidden" name="target_model" value="user_calendars">
    <input type="hidden" name="target_model_id" value="{{$item->id}}">
    <input type="hidden" name="target_user_id" value="{{$item->user_id}}">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            {!!nl2br(__('messages.warning_teacher_change'))!!}
          </label>
          <select name="charge_user_id" class="form-control select2"  width=100% required="true" >
            <option value="">{{__('labels.selectable')}}</option>
            @foreach($teachers as $teacher)
               <option
               value="{{ $teacher->user_id }}"
               >{{$teacher->name()}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
          <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_teacher_change')}}">
            <i class="fa fa-envelope mr-1"></i>
            {{__('labels.send_button')}}
          </button>
      </div>
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
      </div>

      <script>
      $(function(){
        base.pageSettinged("_form", null);
        //submit
      });
      </script>
    </div>
  </form>
  </div>
  @endslot
@endcomponent
