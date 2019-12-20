@section('first_form')
<div class="row">
  @if($item->work!=9)
    @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
    @component('calendars.forms.select_schedule_type', ['_edit'=>$_edit, 'item'=>$item, 'teachers'=>$teachers]); @endcomponent
    @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
  @else
    @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    <div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
      <div class="form-group">
        <label for="remark" class="w-100">
        {{__('labels.remark')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：ミーティング" >@if($_edit==true){{$item->remark}}@endif</textarea>
      </div>
    </div>
  @endif
</div>
@endsection
@section('second_form')
<div class="row">
  @if($item->work!=9)
    @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_work', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_student_group', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @component('calendars.forms.select_student', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @if(isset($teachers) && count($teachers)==1)
      @component('calendars.forms.select_exchanged_calendar', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]);  @endcomponent
      @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'), 'attributes' => $attributes]); @endcomponent
    @endif
  @endif
  <div class="col-12">
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
                    "start_time"=> __('labels.start_date'),
                    "work_time"=> __('labels.timezone'),
                    "place_floor_id_name"=> __('labels.place'),
                    "course_minutes_name"=> __('labels.lesson_time'),
                    "course_type_name"=> __('labels.lesson_type'),
                    "work_name"=> __('labels.work'),
                    "work_time"=> __('labels.datetime'),
                    "student_name"=> __('labels.students'),
                    "subject_name" => __('labels.subject')
                  ];
    ?>
    @foreach($form_data as $key => $name)
    <div class="col-6 p-3 font-weight-bold

      @if($key=="course_type_name" || $key=="course_minutes_name" || $key=="subject_name" || $key=="start_time")
        schedule_type schedule_type_class
      @elseif($key=="work_name" )
        schedule_type schedule_type_other
      @elseif($key=="work_time")
        schedule_type schedule_type_other schedule_type_office_work
      @elseif($key=="student_name")
        schedule_type schedule_type_class schedule_type_other
      @endif
    " >{{$name}}</div>
    <div class="col-6 p-3
      @if($key=="course_type_name" || $key=="course_minutes_name" || $key=="subject_name" || $key=="start_time")
        schedule_type schedule_type_class
      @elseif($key=="work_name" )
        schedule_type schedule_type_other
      @elseif($key=="work_time")
        schedule_type schedule_type_other schedule_type_office_work
      @elseif($key=="student_name")
        schedule_type schedule_type_class schedule_type_other
      @endif
    ">
      <span id="{{$key}}"></span>
      @if($key=="start_time")
        <span class="text-xs add_type add_type_new schedule_type schedule_type_class">
          @if($item->trial_id > 0)
          <small class="badge badge-success mt-1 mr-1">
            {{__('labels.trial_lesson')}}
          </small>
          @elseif($_edit==true)
          <small class="badge badge-secondary mt-1 mr-1">
            {{$item["teaching_name"]}}
          </small>
          @else
          <small class="badge badge-secondary mt-1 mr-1">
            {{__('labels.schedule_add')}}
          </small>
          @endif
        </span>
      @endif
     </div>
      @if($key=="start_time" && $_edit == false)
        <div class="col-12 add_type add_type_exchange px-3 schedule_type schedule_type_class" >
          <span class="text-xs">
            <small class="badge badge-primary mt-1 mr-1 p-1">
              <i class="fa fa-exchange-alt mr-1"></i>
              {{__('labels.exchange')}}: <span id="exchanged_calendar_datetime"></span>
            </small>
          </span>
        </div>
      @elseif($key=="start_time" && $_edit == true && $item["is_exchange"]==true)
        <div class="col-12 px-3 schedule_type schedule_type_class" >
          <span class="text-xs">
            <small class="badge badge-primary mt-1 mr-1 p-1">
              <i class="fa fa-exchange-alt mr-1"></i>
              {{__('labels.exchange')}}: {{$item->exchanged_calendar->details(1)["datetime"]}}
            </small>
          </span>
        </div>
      @endif
    @endforeach
    <div class="col-6 p-3 font-weight-bold">{{__('labels.remark')}}</div>
    <div class="col-6 p-3"><span id="remark"></span></div>
    @component('calendars.forms.mail_send_confirm', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
</div>
@endsection
