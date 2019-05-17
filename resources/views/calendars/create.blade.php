<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($student_id))
    <input type="hidden" value="{{$student_id}}" name="student_id" />
  @endif
  @if(isset($manager_id))
    <input type="hidden" value="{{$manager_id}}" name="manager_id" />
  @endif

<div class="row">
  <div class="col-12 col-lg-6 col-md-6 border-right">
    <div class="row">
      @component('calendars.forms.select_teacher', ['teacher'=>$teacher]); @endcomponent
      @component('calendars.forms.select_student', ['item'=>$item]); @endcomponent
      @component('calendars.forms.select_date', ['item'=>$item, 'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.select_place', ['item'=>$item, 'attributes' => $attributes]); @endcomponent
      @component('students.forms.course_minutes', ['_teacher'=>true, '_edit'=>false, 'teacher'=>$teacher,'attributes' => $attributes]) @endcomponent
    </div>
  </div>
  <div class="col-12 col-lg-6 col-md-6">
    <div class="row">
      @component('calendars.forms.select_lesson', ['item'=>$item, 'teacher'=>$teacher,'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.course_type', ['item'=>$item, 'teacher'=>$teacher,'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.charge_subject', ['teacher'=>$teacher, 'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.select_exchanged_calendar', ['item'=>$item, 'attributes' => $attributes]);  @endcomponent
    </div>
  </div>
</div>

<div class="row">
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
