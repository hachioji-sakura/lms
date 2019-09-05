@section('first_form')
<div class="row">
  @if($item->work!=9)
    @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
    @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
  @endif
  @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  {{-- TODO:ステータス更新はまだ追加しない
  @if(isset($_edit) && $_edit==true)
    @component('calendars.forms.select_status', ['item'=>$item, 'attributes' => $attributes]); @endcomponent
  @endif
  --}}
  @if($item->work!=9)
    @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
  @endif
</div>
@endsection
@section('second_form')
<div class="row">
  @if($item->work!=9)
    @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_student_group', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @component('calendars.forms.select_student', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @if(isset($teachers) && count($teachers)==1)
      @component('calendars.forms.select_exchanged_calendar', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]);  @endcomponent
      @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'), 'attributes' => $attributes]); @endcomponent
    @endif
  @endif
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
                    "start_time"=> __('labels.start_date'),
                    "place_floor_id_name"=> __('labels.place'),
                    "course_minutes_name"=> __('labels.lesson_time'),
                    "course_type_name"=> __('labels.lesson_type'),
                    "student_name"=> __('labels.students'),
                    "subject_name" => __('labels.subject')];
    ?>
    @foreach($form_data as $key => $name)
    <div class="col-6 p-3 font-weight-bold" >{{$name}}</div>
    <div class="col-6 p-3">
      <span id="{{$key}}"></span>
      @if($key=="start_time")
        <span class="text-xs add_type add_type_new">
          @if($item->trial_id > 0)
          <small class="badge badge-success mt-1 mr-1">
            {{__('labels.trial_lesson')}}
          </small>
          @else
          <small class="badge badge-danger mt-1 mr-1">
            {{__('labels.schedule_add')}}
          </small>
          @endif
        </span>
      @endif
     </div>
      @if($key=="start_time")
        <div class="col-12 add_type add_type_exchange px-3" >
          <span class="text-xs">
            <small class="badge badge-primary mt-1 mr-1 p-1">
              <i class="fa fa-exchange-alt mr-1"></i>
              {{__('labels.exchange')}}: <span id="exchanged_calendar_datetime"></span>
            </small>
          </span>
        </div>
      @endif
    @endforeach
    @component('calendars.forms.mail_send_confirm', ['item'=>$item]); @endcomponent
</div>
@endsection
