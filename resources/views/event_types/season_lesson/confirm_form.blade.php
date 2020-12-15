<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-file-invoice mr-1"></i>
    期間講習ご希望コースについて
  </div>
  <div class="col-12 p-2 font-weight-bold" >ご希望の校舎</div>
  <div class="col-12 pl-3"><span id="lesson_place_name"></span></div>
  <div class="col-12 p-2 font-weight-bold" >ご希望のコース</div>
  <div class="col-12 pl-3"><span id="season_lesson_course_name"></span></div>
</div>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-clock mr-1"></i>
    ご希望の日時について
  </div>
  <div class="col-12 p-2 font-weight-bold ">
    ご希望の日時
  </div>
  <div class="col-12">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-center ">日
        </th>
        <th class="p-1 text-center ">時間帯
        </th>
      </tr>
      <tbody id="hope_datetime_list">
      </tbody>
      </table>
    </div>
  </div>
  <div class="col-6 p-2 font-weight-bold" >ご希望の授業日数</div>
  <div class="col-6 p-2"><span id="day_count"></span></div>

  <div class="col-12 subject_confirm">
    <div class="col-12 bg-info p-2 pl-4 mt-4">
      <i class="fa fa-chalkboard-teacher mr-1"></i>
      ご希望の科目と授業数について
    </div>
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-sm text-center">科目</th>
        <th class="p-1 text-sm text-center">
          希望日数
        </th>
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              <tr class="grade-subject" alt="{{$grade}}">
              <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
              <td class="text-center" id="{{$subject}}_day_count">
                -
              </td>
            </tr>
            @endforeach
          @else
            <tr class="grade-subject" alt="{{$grade}}">
            <th class="p-1 text-center bg-gray">{{$subject_data['name']}}</th>
            <th class="p-1 text-center bg-gray subject_name">{{$subject_data['name']}}</th>
            @foreach($attributes['lesson_subject_level'] as $index => $name)
              @if($loop->index == 0)
                @continue
              @endif
              <td class="text-center" id="{{$subject}}_day_count">
                -
              </td>
            @endforeach
            </tr>
          @endisset
        @endforeach
      @endforeach
      </table>
    </div>
  </div>

</div>
<div class="row pb-2">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-question-circle mr-1"></i>
    その他、ご要望等
  </div>

  <div class="col-12 p-2 font-weight-bold" >通常授業を講習に振り替えますか？</div>
  <div class="col-12 pl-3"><span id="regular_schedule_exchange_name"></span></div>

  <div class="col-12 p-2 font-weight-bold" >分割払い可能（3ヶ月）をご希望ですか？</div>
  <div class="col-12 pl-3"><span id="installment_payment_name"></span></div>

  <div class="col-12 p-2 font-weight-bold" >学校の休み期間をおしらせください</div>
  <div class="col-12 pl-3"><span id="school_vacation_date"></span></div>

  <div class="col-12 p-2 font-weight-bold" >特に重視してやって欲しいこと</div>
  <div class="col-12 pl-3"><span id="entry_milestone_name"></span></div>
  <div class="col-12 p-2 font-weight-bold entry_milestone_word_confirm collapse" >特に重視してやって欲しいこと（その他）</div>
  <div class="col-12 pl-3 entry_milestone_word_confirm collapse"><span id="entry_milestone_word"></span></div>
  <div class="col-12 p-2 font-weight-bold" >ご要望につきまして</div>
  <div class="col-12 pl-3"><span id="remark"></span></div>
</div>
