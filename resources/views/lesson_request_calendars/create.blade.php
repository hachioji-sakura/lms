  @include($domain.'.create_form')
  <div id="lesson_request_calendars_entry" class="direct-chat-msg">
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item->id}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="title" class="w-100">
            {{__('labels.students')}}
          </label>
          <span>{{$item->student->name()}}</span>
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="title" class="w-100">
            {{__('labels.lesson_name')}}
          </label>
          <span>{{$item->teaching_type_name}}</span>
        </div>
      </div>
      @if($_edit==true && $item->teaching_type!='training')
      <div class="col-6">
        <div class="form-group">
          <label for="start_date" class="w-100">
            {{__('labels.teachers')}}
          </label>
          <a alt="teacher_name" href="/teachers/{{$item->user->teacher->id}}" target="_blank">
          <i class="fa fa-user-tie mr-1"></i>
          {{$item->user->teacher->name()}}
          </a>
        </div>
      </div>
      @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
      @endif
      @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
      @if($_edit==true && $item->teaching_type!='training')
      @component('lesson_request_calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$item->user->teacher, 'attributes' => $attributes]); @endcomponent
      @endif
      <div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
        <div class="form-group">
          <label for="remark" class="w-100">
          {{__('labels.remark')}}
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：ミーティング" >@if($_edit==true){{$item->remark}}@endif</textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="lesson_request_calendars_entry"
            confirm="{{__('messages.confirm_update')}}">
              {{__('labels.update_button')}}
              <i class="fa fa-caret-right ml-1"></i>
          </button>
      </div>
    </div>
  </form>
</div>
