@section('first_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-clock mr-1"></i>
    {{__('labels.base')}}{{__('labels.info')}}
  </div>
  @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
  @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
  @component('calendar_settings.forms.schedule_method', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
  @component('calendar_settings.forms.lesson_week', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
  @component('calendar_settings.forms.select_time', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
  @component('calendars.forms.select_place', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
  {{--
  @component('calendar_settings.forms.select_enable_date', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  --}}
</div>
@endsection
@section('second_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    {{__('labels.students')}}{{__('labels.info')}}
  </div>
  @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
  @component('calendars.forms.select_student_group', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
  @component('calendars.forms.select_student', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
</div>
@endsection
@section('third_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    {{__('labels.school_lesson')}}{{__('labels.info')}}
  </div>
  @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'), 'attributes' => $attributes]); @endcomponent
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
                    "course_minutes_name"=>__('labels.lesson_time'),
                    "course_type_name"=>__('labels.lesson_type'),
                    "student_name"=>__('labels.students'),
                    "subject_name" => __('labels.subject')];
    ?>
    @foreach($form_data as $key => $name)
    <div class="col-6 p-3 font-weight-bold" >{{$name}}</div>
    <div class="col-6 p-3"><span id="{{$key}}"></span></div>
    @endforeach
</div>
@endsection
