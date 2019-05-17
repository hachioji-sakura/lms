<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <div class="row">
    <div class="col-12 col-lg-6 col-md-6 border-right">
      <div class="row">
        @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teacher'=>$item['teachers'][0]->user->details('teachers')]); @endcomponent
        @component('calendar_settings.forms.schedule_method', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
        @component('calendar_settings.forms.lesson_week', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
        @component('calendar_settings.forms.select_time', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
        @component('calendars.forms.select_place', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
        @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'teacher'=>$item['teachers'][0]->user->details('teachers'),'attributes' => $attributes]) @endcomponent
      </div>
    </div>
    <div class="col-12 col-lg-6 col-md-6">
      <div class="row">
        @component('calendars.forms.select_student', ['_edit'=>$_edit, 'item'=>$item]); @endcomponent
        @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$item['teachers'][0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
        @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$item['teachers'][0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
        @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$item['teachers'][0]->user->details('teachers'), 'attributes' => $attributes]); @endcomponent
      </div>
    </div>
    @component('calendar_settings.forms.select_enable_date', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  </div>
  <div class="row mt-2">
    <div class="col-12 col-lg-6 col-md-6 mb-1">
    <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
      @if(isset($_edit))
        更新する
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
