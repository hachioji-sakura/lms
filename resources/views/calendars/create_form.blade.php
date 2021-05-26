@section('first_form')
<div class="row">
  @if($item->work!=9)
    @if($item->trial_id == 0 && $item["exchanged_calendar_id"]==0 && $_edit==false)
    {{-- 新規授業予定追加時の警告表示 --}}
    <div class="col-12">
      <div class="alert alert-warning text-sm pr-2 schedule_type schedule_type_class">
        <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
        {!!nl2br(__('messages.warning_schedule_add'))!!}
      </div>
    </div>
    @endif
    @if($_edit == true && $item->is_season_lesson()==true)
    {{-- 期間講習予定編集時の警告表示 --}}
    <div class="col-12">
      <div class="alert alert-danger text-sm pr-2 schedule_type schedule_type_class">
        <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
        {!!nl2br(__('messages.warning_season_lesson_edit'))!!}
      </div>
    </div>
    @endif

    @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
    @component('calendars.forms.select_schedule_type', ['user' => $user, '_edit'=>$_edit, 'item'=>$item, 'teachers'=>$teachers]); @endcomponent
    @if(isset($item->trial_id) && $item->trial_id>0)
    <input type="hidden" name="trial_id" value="{{$item->trial_id}}" >
    @endif
    @if(isset($lesson_id) && $lesson_id>0)
    <input type="hidden" name="lesson" value="{{$lesson_id}}" >
    @else
    @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
    @endif
    @if($_edit==true && $item->is_season_lesson()==true && isset($teachers) && $user->role=='teacher')
      {{-- 季節講習の予定を講師が編集した場合 --}}
      <input type="hidden" name="start_time" value="{{date('Y/m/d H:i', strtotime($item->start_time))}}">
      <input type="hidden" name="place_floor_id_name" value="{{$item->place_floor_name}}">
      <div class="col-6">
        <div class="form-group">
          <label for="start_date" class="w-100">
            {{__('labels.date')}}
          </label>
          <div class="input-group">
            {{$item->date}}
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="start_date" class="w-100">
            {{__('labels.place')}}
          </label>
          <div class="input-group">
            {{$item->place_floor_name}}@if($item->is_online()==true)/{{__('labels.online')}}@endif
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="start_date" class="w-100">
            {{__('labels.lesson_time')}}
          </label>
          <div class="input-group">
            {{$item->timezone}}
          </div>
        </div>
      </div>
    @else
      {{-- 登録・編集共通フォーム部分 --}}
      @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
      @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @endif
    
    @if($item->is_teaching()==true)
      @if(isset($lesson_id) && $lesson_id>1 &&  isset($item->trial_id) && $item->trial_id>0)
      {{-- 体験授業かつ、塾以外の場合は、授業時間は30分にする --}}
      <div class="col-12 schedule_type schedule_type_class mb-2">
          <label for="course_minutes" class="w-100">
            {{__('labels.lesson_time')}}
          </label>
          <input type="hidden" name="course_minutes" value="30">
          <input type="hidden" name="course_minutes_name" value="{{$attributes['course_minutes'][30]}}">
          <span>{{$attributes['course_minutes'][30]}} ({{__('labels.trial_lesson')}})</span>
      </div>
      @else
        @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
      @endif
    @endif
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
    <div class="col-6 p-2 font-weight-bold

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
    <div class="col-6 p-2
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
      @if($key=="start_time" && $_edit == false && $item->trial_id == 0)
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
    <div class="col-6 p-2 font-weight-bold">{{__('labels.remark')}}</div>
    <div class="col-6 p-2"><span id="remark"></span></div>
    @if(isset($teachers) && $user->role=='manager' && $_edit==false)
    <div class="col-12">
      <div class="alert alert-danger text-sm">
        <i class="icon fa fa-exclamation-triangle"></i>この予定はダミーで登録します
      </div>
    </div>
    @else
    @component('calendars.forms.mail_send_confirm', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
    @endif
</div>
@endsection
