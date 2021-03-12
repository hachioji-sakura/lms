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
      @if($_edit==true && $item->is_season_lesson()==true)
        {{-- 期間講習予定編集時の警告表示 --}}
        <div class="col-12">
          <div class="alert alert-danger text-sm pr-2 schedule_type schedule_type_class">
            <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
            {!!nl2br(__('messages.warning_season_lesson_edit'))!!}
          </div>
        </div>
      @endif
      @component('textbooks.forms.select_textbook', ['_edit'=>$_edit, 'textbook'=>$textbook ]); @endcomponent
      @component('textbooks.forms.select_publisher', ['_edit' => $_edit, 'publishers' => $publishers, 'textbook'=>$textbook ]); @endcomponent
      @component('textbooks.forms.select_supplier', ['_edit' => $_edit, 'suppliers' => $suppliers, 'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.select_difficulty', ['_edit' => $_edit,'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.subject', ['_edit' => $_edit,'subjects' => $subjects,'textbookSubjects' => $textbookSubjects]); @endcomponent
      @component('textbooks.forms.grade', ['_edit' => $_edit,'grades' => $grades,'textbookGrades' => $textbookGrades]); @endcomponent
      @component('textbooks.forms.price', ['_edit' => $_edit,'grades' => $grades,'textbookPrices'=>$textbookPrices]); @endcomponent
      @component('textbooks.forms.explain', ['_edit' => $_edit, 'textbook' => $textbook ]); @endcomponent


{{--      @component('calendars.forms.select_schedule_type', ['user' => $user, '_edit'=>$_edit, 'item'=>$item, 'teachers'=>$teachers]); @endcomponent--}}
      @if(isset($item->trial_id) && $item->trial_id>0)
        <input type="hidden" name="trial_id" value="{{$item->trial_id}}" >
      @endif
      @if(isset($lesson_id) && $lesson_id>0)
        <input type="hidden" name="lesson" value="{{$lesson_id}}" >
      @else
{{--        @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent--}}
      @endif
      @if($_edit==true && $item->is_season_lesson()==true && isset($teachers) && $user->role=='teacher')
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
{{--      @else--}}
{{--        @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent--}}

{{--        @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent--}}
      @endif
      @if(isset($lesson_id) && $lesson_id>1 && isset($item->trial_id) && $item->trial_id>0)
         体験授業かつ、塾以外の場合は、授業時間は30分にする
        <div class="col-12 schedule_type schedule_type_class mb-2">
          <label for="course_minutes" class="w-100">
            {{__('labels.lesson_time')}}
          </label>
          <input type="hidden" name="course_minutes" value="30">
          <input type="hidden" name="course_minutes_name" value="{{$attributes['course_minutes'][30]}}">
          <span>{{$attributes['course_minutes'][30]}} ({{__('labels.trial_lesson')}})</span>
        </div>
      @else
{{--        @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent--}}
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
