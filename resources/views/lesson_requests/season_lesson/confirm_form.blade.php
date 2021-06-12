<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-file-invoice mr-1"></i>
    期間講習ご希望コースについて
  </div>
  <div class="col-12 p-2 font-weight-bold" >ご希望の校舎</div>
  <div class="col-12 pl-3"><span id="lesson_place_name">
    @if(isset($item)){{$item->get_tags_name('lesson_place')}}@endif
  </span></div>
  <div class="col-12 p-2 font-weight-bold" >ご希望のコース</div>
  <div class="col-12 pl-3"><span id="season_lesson_course_name">
    @if(isset($item)){{$item->get_tags_name('season_lesson_course')}}@endif
  </span></div>
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
        @if(isset($item))
          @foreach($item->request_dates as $request_date)
          <tr>
          <td class="bg-gray">{{$request_date->month_day}}</td>
          <td>{{$request_date->timezone}}</td>
          </tr>
          @endforeach
        @endif
      </tbody>
      </table>
    </div>
  </div>
  <div class="col-12 bg-info p-2 pl-4 mt-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    ご希望の科目と授業数について
  </div>
  <div class="col-12 subject_confirm">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-sm text-center">科目</th>
        <th class="p-1 text-sm text-center">
          希望授業コマ数
        </th>
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              @if(isset($item) && $item->get_tag_value($subject.'_day_count') == 0) @continue @endif
              <tr class="grade-subject" alt="{{$grade}}">
              <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
              <td class="text-center" id="{{$subject}}_day_count">
                @if(isset($item))
                {{$item->get_tag_value($subject.'_day_count')}}
                @else
                -
                @endif
              </td>
            </tr>
            @endforeach
          @else
            @if(isset($item) && $item->get_tag_value($subject.'_day_count') == 0) @continue @endif
            <tr class="grade-subject" alt="{{$grade}}">
            <th class="p-1 text-center bg-gray">{{$subject_data['name']}}</th>
            <td class="text-center" id="{{$subject}}_day_count">
              @if(isset($item))
                {{$item->get_tag_value($subject.'_day_count')}}
              @else
              -
              @endif
            </td>
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
  <div class="col-12 pl-3"><span id="regular_schedule_exchange_name">
    @if(isset($item) && $item->has_tag('regular_schedule_exchange', 'true'))はい @elseいいえ@endif
  </span></div>

  <div class="col-12 p-2 font-weight-bold" >分割払い可能（3ヶ月）をご希望ですか？</div>
  <div class="col-12 pl-3"><span id="installment_payment_name">
    @if(isset($item) && $item->has_tag('installment_payment', 'true'))はい @elseいいえ@endif
  </span></div>

  <div class="col-12 p-2 font-weight-bold" >学校の休み期間をおしらせください</div>
  <div class="col-12 pl-3"><span id="school_vacation_date">
    @if(isset($item)){{$item->get_tag_name('school_vacation_start_date')}}～{{$item->get_tag_name('school_vacation_end_date')}}@endif
  </span></div>

  <div class="col-12 p-2 font-weight-bold" >特に重視してやって欲しいこと</div>
  <div class="col-12 pl-3"><span id="entry_milestone_name">
    @if(isset($item)){{$item->get_tags_name('entry_milestone')}}@endif
  </span></div>
  <div class="col-12 p-2 font-weight-bold entry_milestone_word_confirm " >特に重視してやって欲しいこと（その他）</div>
  <div class="col-12 pl-3 entry_milestone_word_confirm "><span id="entry_milestone_word">
    @if(isset($item)){{$item->get_tag_value('entry_milestone_word')}}@endif
  </span></div>
  <div class="col-12 p-2 font-weight-bold" >ご要望につきまして</div>
  <div class="col-12 pl-3"><span id="remark">
    @if(isset($item)){!!nl2br($item->remark)!!}@endif
  </span></div>
</div>
