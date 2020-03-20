@section('first_form')
<div class="row">
  <?php
  $teacher = null;
  if(count($teachers) > 0) $teacher = $teachers[0]->user->details('teachers');
  ?>
  <input type="hidden" value="{{$item->work}}" name="work" >
  @if($item->work!=9)
    @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
    @component('calendars.forms.select_schedule_type', ['_edit'=>$_edit, 'item'=>$item, 'teachers'=>$teachers, 'is_class_schedule' => true]); @endcomponent
    @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher' => $teacher,'attributes' => $attributes]); @endcomponent
    @component('calendar_settings.forms.schedule_method', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'teacher' => $teacher]) @endcomponent
    @component('calendar_settings.forms.lesson_week', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'teacher' => $teacher]) @endcomponent
    @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
  @else
    <input type="hidden" value="office_work" name="schedule_type" >
    @component('calendar_settings.forms.schedule_method', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'teacher' => $teacher]) @endcomponent
    @component('calendar_settings.forms.lesson_week', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'teacher' => $teacher]) @endcomponent
    @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  @endif

  @component('calendar_settings.forms.select_enable_date', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
</div>
@endsection
@section('second_form')
<div class="row">
  @if($item->work!=9)
    @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher' => $teacher,'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_student_group', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @component('calendars.forms.select_student', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
  @endif
</div>
@endsection
@section('third_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4 schedule_type schedule_type_class">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    {{__('labels.school_lesson')}}{{__('labels.info')}}
  </div>
  @if($item->work!=9)
    @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher' => $teacher, 'attributes' => $attributes]); @endcomponent
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
@endsection
@section('confirm_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    {{__('labels.confirm_title')}}
  </div>
    <?php
      $form_data = ["teacher_name" => __('labels.teachers'),
                    "schedule_name"=>__('labels.week_day'),
                    "place_floor_id_name"=>__('labels.place'),
                    "start_time"=>__('labels.start_time'),
                    "course_minutes_name"=>__('labels.lesson_time'),
                    "course_type_name"=>__('labels.lesson_type'),
                    "student_name"=>__('labels.students'),
                    "subject_name" => __('labels.subject'),
                    "enable_dulation" => "有効期間",
                  ];
    ?>
    @foreach($form_data as $key => $name)
    <div class="col-6 p-3 font-weight-bold" >{{$name}}</div>
    <div class="col-6 p-3"><span id="{{$key}}"></span></div>
    @endforeach
    @component('calendars.forms.mail_send_confirm', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
</div>
@endsection
