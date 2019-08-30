@section('parent_form')
<div class="row">
  @foreach($item->trial_students as $trial_student)
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
      <rb>{{$trial_student->student->name()}}</rb>
      <rt>{{$trial_student->student->kana()}}</rt>
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3">{{$trial_student->student->gender()}}</div>
  {{--
    <div class="col-6 p-3 font-weight-bold" >生年月日</div>
    <div class="col-6 p-3">{{$trial_student->student->birth_day()}}</div>
  --}}
  <div class="col-12">
    @component('components.select_birthday', ['prefix'=>'']) @endcomponent
  </div>
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3">{{$trial_student->student->grade()}}</div>
  <div class="col-6 p-3 font-weight-bold school_name_confirm" >学校名</div>
  <div class="col-6 p-3 school_name_confirm">{{$trial_student->student->school_name()}}</div>
  @endforeach
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    ご契約者様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'parent_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'parent_']) @endcomponent
  @component('students.forms.email', ['item' => ['email' => $item['parent_email']], 'is_label' => true]) @endcomponent
  @component('students.forms.phoneno', ['item' => ['phone_no' => $item['parent_phone_no']]]) @endcomponent
  @component('students.forms.address', ['item' => ['address' => $item['parent_address']]]) @endcomponent
  {{--
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'student_', 'item' => $item->student, 'is_label'=>true]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student_', 'item' => $item->student, 'is_label'=>true]) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    {{$item->student->gender()}}
  </div>
  @component('students.forms.school', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $item->student]) @endcomponent
  --}}
</div>
@endsection

@section('admission_form')
  @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $item]) @endcomponent
@endsection
@section('aa')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-calendar-alt mr-1"></i>
    通塾スケジュールにつきまして
  </div>
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
  <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
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
  <div class="col-12 bg-info p-2 pl-4 english_talk_confirm">
    <i class="fa fa-receipt mr-1"></i>
    料金につきまして
  </div>
  <div class="col-6 p-3 font-weight-bold" >受講料（学年、教科、マンツーマン or グループ、週１ or 週２、〇〇分）</div>
  <div class="col-6 p-3">99,999円</div>
  <div class="col-6 p-3 font-weight-bold" >月会費</div>
  <div class="col-6 p-3">1,500円（税抜き）</div>
  <div class="col-6 p-3 font-weight-bold" >入会金</div>
  <div class="col-6 p-3">15,000円（税抜き）</div>
</div>
@endsection
