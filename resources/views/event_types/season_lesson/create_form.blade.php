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
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'attributes' => $attributes]) @endcomponent
  @component('event_types.season_lesson.course', ['_edit'=>$_edit, 'attributes' => $attributes]) @endcomponent
  <div class="col-12 mt-1">
    <label for="season_lesson_course" class="w-100">
      時間帯について
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <label class="mx-2" for="hope_timezone_am">
      <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_timezone" id="hope_timezone_am" value="am"
        @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "am"))
        checked
        @endif
        onChange="hope_timezone_all_set()"
        required="true">
        午前(11:00-16:00）
    </label>
    <label class="mx-2" for="hope_timezone_pm">
      <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_timezone" id="hope_timezone_pm" value="pm"
        @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "pm"))
        checked
        @endif
        onChange="hope_timezone_all_set()"
        required="true">
        午後(13:00-18:00）
    </label>

  </div>
  <div class="col-12 mt-1 mb-2">
    <div class="input-group">
      <label class="mx-2" for="hope_timezone_order">
        <input class="form-check-input icheck flat-red ml-1" type="radio" name="hope_timezone" id="hope_timezone_order" value="order"
          @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "order"))
          checked
          @endif
          onChange="hope_timezone_all_set()"
          required="true">
          指定
      </label>
      <select name="hope_start_time" class="form-control mw-80px" required="true" disabled>
        <option value="">{{__('labels.selectable')}}</option>
        @for ($h = 8; $h < 23; $h++)
          <option value="{{$h}}"
          @if($_edit===true && 1==2)
          selected
          @endif
          >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
        @endfor
      </select>
      <span class="mt-2 ml-2">時 ～</span>
      <select name="hope_end_time" class="form-control mw-80px" required="true" greater="hope_start_time" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="hope_start_time" not_equal_error="{{__('messages.validate_timezone_error')}}" disabled>
        <option value="">{{__('labels.selectable')}}</option>
        @for ($h = 8; $h < 23; $h++)
          <option value="{{$h}}"
          @if($_edit===true && 1==2)
          selected
          @endif
          >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
          @endfor
      </select>
      <span class="mt-2 ml-2">時</span>
    </div>
  </div>
</div>
<script>
function hope_timezone_all_set(){
  var timezone = $("input[name='hope_timezone']:checked").val();
  if(!timezone) return;
  if(timezone=="am" || timezone=="pm"){
    $("select[name='hope_start_time']").prop('disabled', true);
    $("select[name='hope_end_time']").prop('disabled', true);
    if(timezone=='am'){
      $("select[name='hope_start_time']").val(11);
      $("select[name='hope_end_time']").val(16);
    }
    else {
      $("select[name='hope_start_time']").val(13);
      $("select[name='hope_end_time']").val(18);
    }
  }
  else {
    $("select[name='hope_start_time']").prop('disabled', false);
    $("select[name='hope_end_time']").prop('disabled', false);
  }
  console.log('hoge:'+timezone);
  $('input.hope_date_timezone[value="'+timezone+'"]').each(function(){
    $(this).iCheck('check');
  });
  $('select.hope_date_start_time').val($("select[name='hope_start_time']").val());
  $('select.hope_date_end_time').val($("select[name='hope_end_time']").val());
}
</script>
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
  @component('event_types.season_lesson.subject', ['_edit'=>$_edit,  'attributes' => $attributes, '_teacher' => false, 'category_display' => false, 'grade_display' => false]) @endcomponent
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
  @component('event_types.season_lesson.hope_datetime', ['_edit'=>$_edit, 'start_date'=>$event->event_from_date, 'end_date' => $event->event_to_date ,'attributes' => $attributes]) @endcomponent
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
  <div class="col-12">
    <div class="form-group">
      <label for="installment_payment">
        分割払い可能（3ヶ月）をご希望ですか？
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

  @component('students.forms.entry_milestone', ['_edit'=>$_edit, 'event'=>$event, 'attributes' => $attributes]) @endcomponent

  @component('students.forms.remark', ['_edit'=>$_edit, 'event'=>$event, 'attributes' => $attributes]) @endcomponent

</div>
@endsection
