<?php $debug = true ?>
@section('first_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    期間講習ご希望コースについて
  </div>
  <script>
  $(function(){
    lesson_checkbox_change($('input[name="lesson[]"]'));
    $('*[name="grade"]').change();
  });
  </script>
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'attributes' => $attributes, 'item' => $item]) @endcomponent
  @component('lesson_requests.season_lesson.course', ['_edit'=>$_edit, 'attributes' => $attributes, 'item' => $item]) @endcomponent
  @if($_edit==false)
  @component('lesson_requests.season_lesson.hope_timezone', ['_edit'=>$_edit, 'attributes' => $attributes, 'is_student' => true, 'item' => $item]) @endcomponent
  @endif
</div>

@endsection



@section('subject_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    ご希望の科目と授業数について
  </div>
  <div class="col-12">
    <h6 class="text-sm p-2 pl-3 bg-success" >
      授業コマ数は、60分ごとで1（120分の場合、2)となります。<br>
      希望科目数と、授業コマ数が一致するように入力してください
    </h6>
  </div>
  @component('lesson_requests.season_lesson.subject', ['_edit'=>$_edit,  'attributes' => $attributes, '_teacher' => false, 'category_display' => false, 'grade_display' => false, 'item'=>$item]) @endcomponent
</div>
@endsection

@section('hope_datetime')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-clock mr-1"></i>
    ご希望の日時について
  </div>
  <div class="col-12">
    <h6 class="text-sm p-2 pl-3 bg-success" >
      受講を希望する日付にチェックを入れて、受講時間を指定してください。<br><br>
      受講日数分の日程がまだ不明な場合は、現在判明している分のみチェックを入れてください。<br>
    </h6>
  </div>
  @component('lesson_requests.season_lesson.hope_datetime', ['_edit'=>$_edit, 'event_dates' => $event_dates ,'attributes' => $attributes, 'is_student' => !$_edit, 'item' => $item]) @endcomponent
</div>
@endsection

@section('survey_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-question-circle mr-1"></i>
    その他、ご要望等
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
            @if($debug==true && $_edit==false) checked @endif
            @if($_edit==true && $item->has_tag('regular_schedule_exchange', 'true')==true) checked @endif
            >
            <label class="form-check-label" for="regular_schedule_exchange_t">
                {{__('labels.yes')}}
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-red" type="radio" name="regular_schedule_exchange" id="regular_schedule_exchange_f" value="false" required="true"
            @if($_edit==true && $item->has_tag('regular_schedule_exchange', 'true')==false) checked @endif
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
        <input type="text" name="school_vacation_start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="yyyy/mm/dd"
        @if($debug==true && $_edit==false) value="2020/12/26" @endif
        @if($_edit==true)
        value="{{$item->get_tag_value('school_vacation_start_date')}}"
        @endif

        >

        <div class="input-group-append">
          <span class="input-group-text">～</span>
        </div>
        <input type="text"  name="school_vacation_end_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="yyyy/mm/dd"
        @if($debug==true && $_edit==false) value="2021/01/06" @endif
        @if($_edit==true)
        value="{{$item->get_tag_value('school_vacation_end_date')}}"
        @endif

        >
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="installment_payment">
        分割払い可能（3ヶ月）をご希望ですか？
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group">
        <div class="form-check">
            <input class="form-check-input icheck flat-red" type="radio" name="installment_payment" id="installment_payment_t" value="true" required="true"
            @if($debug==true && $_edit==false) checked @endif
            @if($_edit==true && $item->has_tag('installment_payment', 'true')==true) checked @endif
            >
            <label class="form-check-label" for="installment_payment_t">
                {{__('labels.yes')}}
            </label>
        </div>
        <div class="form-check ml-2">
            <input class="form-check-input icheck flat-red" type="radio" name="installment_payment" id="installment_payment_f" value="false" required="true"
            @if($_edit==true && $item->has_tag('installment_payment', 'true')==false) checked @endif
            >
            <label class="form-check-label" for="installment_payment_f">
                {{__('labels.no')}}
            </label>
        </div>
      </div>
    </div>
  </div>

  @component('students.forms.entry_milestone', ['_edit'=>$_edit, 'item' => $item, 'event'=>$event, 'attributes' => $attributes]) @endcomponent

  @component('students.forms.remark', ['_edit'=>$_edit, 'item' => $item, 'event'=>$event, 'attributes' => $attributes]) @endcomponent

</div>
@endsection