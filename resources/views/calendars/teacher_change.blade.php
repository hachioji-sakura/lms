@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @endslot
  @slot('forms')
  <div id="_form">
  @if(isset($mantenance) && $maintenance == true)
  {{__('messages.info_teacher_change_maintenance')}}
  <form method="POST" action="/calendars/{{$item->id}}/teacher_change">
    @method('PUT')
  @else
  <form method="POST" action="/asks">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    {{--注意
    <input type="hidden" name="parent_ask_id" value="{{$ask->id}}">
    --}}
    <input type="hidden" name="type" value="teacher_change">
    <input type="hidden" name="target_model" value="user_calendars">
    <input type="hidden" name="target_model_id" value="{{$item->id}}">
    <input type="hidden" name="target_user_id" value="{{$item->user_id}}">
    @if(!$item->is_teacher_changing())
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            @if(isset($maintenance) && $maintenance == true)
            {{__('labels.teachers')}}{{__('labels.select')}}
            @else
            {!!nl2br(__('messages.warning_teacher_change'))!!}
            @endif
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
    @endif
    <div class="row">
      <div class="col-12 mb-1">
        @if(isset($maintenance) && $maintenance == true)
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_update')}}" {{$item->is_teacher_changing() ? 'disabled' : ''}}>
          <i class="fa fa-sync mr-1"></i>
          {{__('labels.update_button')}}
        </button>
        @else
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="{{__('messages.confirm_teacher_change')}}" {{$item->is_teacher_changing() ? 'disabled' : ''}}>
          @if($item->is_teacher_changing())
          <i class="fa fa-sync mr-1"></i>
          {{__('labels.adjust_schedule_list')}}
          @else
          <i class="fa fa-envelope mr-1"></i>
          {{__('labels.send_button')}}
          @endif
        </button>
        @endif
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
