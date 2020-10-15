@section('first_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    #event_title#
  </div>
  <input type="hidden" name="lesson[]" value="1" />
  <input type="hidden" class="grade" name="grade" value="e1" />
  <input type="hidden" name="grade_name" value="小1" />
  <script>
  $(function(){
    lesson_checkbox_change($('input[name="lesson[]"]'));
    $('*[name="grade"]').change();
  });
  </script>
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('event_types.forms.season_school_lesson_milestone', ['_edit'=>$_edit, 'start_date'=>'2020-07-23', 'end_date' => '2020-08-31', 'item'=>$item,'attributes' => $attributes]) @endcomponent
  <div class="col-12">
    <div class="form-group">
      <label for="installment_payment">
        分割払い可能（3ヶ月）をご希望ですか
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="form-check">
            <input class="form-check-input icheck flat-red" type="radio" name="installment_payment" id="installment_payment_t" value="true" required="true"
            >
            <label class="form-check-label" for="installment_payment_t">
                {{__('labels.yes')}}
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-red" type="radio" name="installment_payment" id="installment_payment_f" value="false" required="true"
            >
            <label class="form-check-label" for="installment_payment_f">
                {{__('labels.no')}}
            </label>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="regular_schedule_exchange">
        通常授業を講習に振り替えますか？
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="form-check">
            <input class="form-check-input icheck flat-red" type="radio" name="regular_schedule_exchange" id="regular_schedule_exchange_t" value="true" required="true"
            >
            <label class="form-check-label" for="regular_schedule_exchange_t">
                {{__('labels.yes')}}
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-red" type="radio" name="regular_schedule_exchange" id="regular_schedule_exchange_f" value="false" required="true"
            >
            <label class="form-check-label" for="regular_schedule_exchange_f">
                {{__('labels.no')}}
            </label>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="start_date" class="w-100">
        学校の休み期間をおしらせください
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text"  name="school_vacation_start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="yyyy/mm/dd"
        >

        <div class="input-group-append">
          <span class="input-group-text">～</span>
        </div>
        <input type="text"  name="school_vacation_end_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="yyyy/mm/dd"

        >
      </div>
    </div>
  </div>

</div>
<div class="row">

  @component('event_types.forms.subject', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => false, 'category_display' => false, 'grade_display' => false]) @endcomponent
</div>
@endsection

@section('hope_datetime')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    ご希望の日時について
  </div>

  @component('event_types.forms.hope_datetime', ['_edit'=>$_edit, 'start_date'=>'2020-07-23', 'end_date' => '2020-08-31', 'item'=>$item,'attributes' => $attributes]) @endcomponent
</div>
@endsection


@section('lesson_week_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-calendar-alt mr-1"></i>
    通塾スケジュールにつきまして
  </div>
  @component('students.forms.lesson_week_count', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher' => false, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item, 'prefix' => 'lesson', 'attributes' => $attributes, 'title' => 'ご希望の通塾曜日・時間帯']) @endcomponent
</div>
@endsection

@section('subject_form')

@endsection

@section('survey_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-question-circle mr-1"></i>
    サービス向上のためアンケートをご記入ください
  </div>
  @component('students.forms.remark', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @if(!isset($user->role))
    @component('students.forms.howto', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @endif
</div>
@endsection
