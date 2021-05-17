@isset($is_trial)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  <div class="col-6 p-3 font-weight-bold" >ご希望のレッスン</div>
  <div class="col-6 p-3"><span id="lesson_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第１希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time1"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第２希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time2"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第３希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time3"></span></div>
{{-- TODO 後回し
  <div class="col-6 p-3 font-weight-bold" >第４希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time4"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第５希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time5"></span></div>
--}}
  <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >体験授業当日に入会等の説明を希望しますか</div>
  <div class="col-6 p-3"><span id="parent_interview_name"></span></div>
</div>
@if((!isset($_edit) || $_edit!=true) && !(isset($is_already_registered_student) && $is_already_registered_student==true))
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
      <rb><span id="student_name_last"></span>&nbsp;<span id="student_name_first"></span></rb>
      <rt><span id="student_kana_last"></span>&nbsp;<span id="student_kana_first"></span></rt>
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >生年月日</div>
  <div class="col-6 p-3"><span id="birth_day"></span></div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3"><span id="gender_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3"><span id="grade_name"></span></div>
  <div class="col-6 p-3 font-weight-bold grade_school_name_confirm" >学校名</div>
  <div class="col-6 p-3 grade_school_name_confirm"><span id="school_name"></span></div>
</div>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-phone-square mr-1"></i>
    ご連絡先
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
      <rb><span id="parent_name_last"></span>&nbsp;<span id="parent_name_first"></span></rb>
      <rt><span id="parent_kana_last"></span>&nbsp;<span id="parent_kana_first"></span></rt>
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >メールアドレス</div>
  <div class="col-6 p-3"><span id="email"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご連絡先</div>
  <div class="col-6 p-3"><span id="phone_no"></span></div>
  <div class="col-6 p-3 font-weight-bold" >住所</div>
  <div class="col-6 p-3"><span id="address"></span></div>
</div>
@endif
@endisset

<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-calendar-alt mr-1"></i>
    通塾スケジュールにつきまして
  </div>
  @empty($is_trial)
  <div class="col-6 p-3 font-weight-bold" >授業開始希望日</div>
  <div class="col-6 p-3"><span id="schedule_start_hope_date"></span></div>
  @endempty
  <div class="col-6 p-3 font-weight-bold" >ご希望の授業回数</div>
  <div class="col-6 p-3">週<span id="lesson_week_count_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望の授業時間</div>
  <div class="col-6 p-3"><span id="course_minutes_name"></span></div>
  <div class="col-12 p-3 font-weight-bold">
    ご希望の曜日・時間帯
  </div>
  <div class="col-12">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-center">時間帯 / 曜日</th>
        @foreach($attributes['lesson_week'] as $index => $name)
        <th class="p-1 text-center lesson_week_label" atl="{{$index}}">
           {{$name}}
        </th>
        @endforeach
      </tr>
      @foreach($attributes['lesson_time'] as $index => $name)
      <tr class="">
        <th class="p-1 text-center bg-gray text-sm lesson_week_time_label">{{$name}}</th>
        @foreach($attributes['lesson_week'] as $week_code => $week_name)
        <td class="p-1 text-center" id="lesson_{{$week_code}}_time_{{$index}}_name">
          -
        </td>
        @endforeach
      </tr>
      @endforeach
      </table>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 subject_confirm">
    <i class="fa fa-pen-square mr-1"></i>
    塾の授業内容につきまして
  </div>
  <div class="col-12 p-3 font-weight-bold subject_confirm">
    ご希望の科目
  </div>
  <div class="col-12 subject_confirm">
    <div class="form-group">
      <table class="table table-striped">
      <tr class="bg-gray">
        <th class="p-1 text-sm text-center">分類</th>
        <th class="p-1 text-sm text-center">科目</th>
        <th class="p-1 text-sm text-center">
          補習授業
        </th>
        <th class="p-1 text-sm text-center">
          受験対策
        </th>
      </tr>
      @foreach(config('charge_subjects') as $grade => $subject_group)
        @foreach($subject_group as $subject => $subject_data)
          <?php $l1 = $loop->index; ?>
          @isset($subject_data['items'])
            @foreach($subject_data['items'] as $subject => $subject_name)
              <tr class="grade-subject" alt="{{$grade}}">
              @if($loop->index===0)
              <th class="p-1 text-center bg-gray" rowspan={{count($subject_data['items'])}}>{{$subject_data['name']}}</th>
              @endif
              <th class="p-1 text-center bg-gray subject_name">{{$subject_name}}</th>
              @foreach($attributes['lesson_subject_level'] as $index => $name)
                @if($loop->index == 0)
                  @continue
                @endif
                <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
                  -
                </td>
              </td>
              @endforeach
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
              <td class="text-center" id="{{$subject}}_level_{{$index}}_name">
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
  <div class="col-12 bg-info p-2 pl-4 english_talk_confirm">
    <i class="fa fa-comments mr-1"></i>
    英会話の授業内容につきまして
  </div>
  <div class="col-6 p-3 font-weight-bold english_talk_confirm" >ご希望の英会話講師</div>
  <div class="col-6 p-3 english_talk_confirm"><span id="english_teacher_name"></span></div>
  <div class="col-6 p-3 font-weight-bold english_talk_confirm" >ご希望の英会話講師</div>
  <div class="col-6 p-3 english_talk_confirm"><span id="english_talk_lesson_name"></span></div>
  <div class="col-6 p-3 font-weight-bold english_talk_confirm" >授業形式のご希望をお知らせください</div>
  <div class="col-6 p-3 english_talk_confirm"><span id="english_talk_course_type_name"></span></div>
  <div class="col-12 bg-info p-2 pl-4 piano_confirm">
    <i class="fa fa-music mr-1"></i>
    ピアノの授業内容につきまして
  </div>
  <div class="col-6 p-3 font-weight-bold piano_confirm" >ピアノのご経験につきまして</div>
  <div class="col-6 p-3 piano_confirm"><span id="piano_level_name"></span></div>
  <div class="col-12 bg-info p-2 pl-4 kids_lesson_confirm">
    <i class="fa fa-shapes mr-1"></i>
    習い事の授業内容につきまして
  </div>
  <div class="col-6 p-3 font-weight-bold kids_lesson_confirm" >ご希望の習い事につきましてお知らせください</div>
  <div class="col-6 p-3 kids_lesson_confirm"><span id="kids_lesson_name"></span></div>
  <div class="col-6 p-3 font-weight-bold kids_lesson_confirm" >授業形式のご希望をお知らせください</div>
  <div class="col-6 p-3 kids_lesson_confirm"><span id="kids_lesson_course_type_name"></span></div>
</div>
@if(!isset($_edit) || $_edit!=true)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-question-circle mr-1"></i>
    アンケート
  </div>
  <div class="col-6 p-3 font-weight-bold" >特に重視してやって欲しいこと</div>
  <div class="col-6 p-3"><span id="entry_milestone_name"></span></div>
  <div class="col-6 p-3 font-weight-bold entry_milestone_word_confirm collapse" >特に重視してやって欲しいこと（その他）</div>
  <div class="col-6 p-3 entry_milestone_word_confirm collapse"><span id="entry_milestone_word"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご要望につきまして</div>
  <div class="col-6 p-3"><span id="remark"></span></div>
  @isset($is_trial)
  <div class="col-6 p-3 font-weight-bold" >当塾をお知りになった方法は何でしょうか？</div>
  <div class="col-6 p-3"><span id="howto_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第１志望校</div>
  <div class="col-6 p-3"><span id="choice_school1"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第２志望校</div>
  <div class="col-6 p-3"><span id="choice_school2"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第３志望校</div>
  <div class="col-6 p-3"><span id="choice_school3"></span></div>
  <div class="col-6 p-3 font-weight-bold howto_word_confirm collapse" >検索ワードをお答えください</div>
  <div class="col-6 p-3 howto_word_confirm collapse"><span id="howto_word"></span></div>
  @endif
</div>
@endisset
