@section('parent_form')
@isset($parent)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    お客様情報
  </div>
  @component('students.forms.name', ['item' => $parent, 'prefix' => 'parent_']) @endcomponent
  @component('students.forms.kana', ['item' => $parent, 'prefix' => 'parent_']) @endcomponent

  <div class="col-12 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="email" class="w-100">
        メールアドレス
      </label>
      <span>{{$parent->email}}</span>
    </div>
  </div>
  @component('students.forms.password', ['item' => $parents, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.phoneno', ['item' => $parents, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.address', ['item' => $parents, 'attributes' => $attributes]) @endcomponent

</div>
@endisset
@endsection

@section('student_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', ['item' => $student, 'prefix' => 'student_', 'is_label' => true])
  @endcomponent
  @component('students.forms.kana', ['item' => $student, 'prefix' => 'student_'])
  @endcomponent

  <div class="col-10">
    @component('components.select_birthday', ['item' => $student])
    @endcomponent
  </div>
  <div class="col-2 col-lg-2 col-md-2">
    <div class="form-group">
      <label for="gender" class="w-100">
        性別
      </label>
      <span id="gender_name">{{$student->gender()}}</span>
    </div>
  </div>
  @component('students.forms.school', ['item' => $student, 'attributes' => $attributes]) @endcomponent
</div>
@endsection
@section('lesson_week_form')
<div class="row">
  @component('students.forms.lesson_week_count', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.lesson_week', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.lesson_place', ['item' => $student, 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('subject_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    お申込み内容
  </div>
  @component('students.forms.lesson', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.course_minutes', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.subject', ['_edit'=>$_edit,'item' => $student, 'attributes' => $attributes, 'category_display' => false, 'grade_display' => false]) @endcomponent
  @component('students.forms.course_type', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.english_teacher', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.piano_level', ['item' => $student, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.kids_lesson', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.remark', ['item' => $student, 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('confirm_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
    <ruby style="ruby-overhang: none">
      <rb><span id="student_name_last">{{$student->name_last}}</span>&nbsp;<span id="student_name_first">{{$student->name_first}}</span></rb>
      <rt><span id="student_kana_last"></span>&nbsp;<span id="student_kana_first"></span></rt>
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3"><span id="gender_name">{{$student->gender()}}</span></div>
  <div class="col-6 p-3 font-weight-bold" >生年月日</div>
  <div class="col-6 p-3"><span id="birth_day"></span></div>
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3"><span id="grade_name"></span></div>
  <div class="col-6 p-3 font-weight-bold school_name_confirm" >学校名</div>
  <div class="col-6 p-3 school_name_confirm"><span id="school_name"></span></div>
</div>
@isset($parent)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-user-friends mr-1"></i>
    保護者様情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >氏名・フリガナ</div>
  <div class="col-6 p-3">
  <ruby style="ruby-overhang: none">
  <rb><span id="parent_name_last"></span>&nbsp;<span id="parent_name_first"></span></rb>
  <rt><span id="parent_kana_last"></span>&nbsp;<span id="parent_kana_first"></span></rt>
  </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >メールアドレス</div>
  <div class="col-6 p-3"><span id="email">{{$parent->email}}</span></div>
  <div class="col-6 p-3 font-weight-bold" >ご連絡先</div>
  <div class="col-6 p-3"><span id="phone_no"></span></div>
</div>
@endisset
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    お申込み情報
  </div>
  <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望のレッスン</div>
  <div class="col-6 p-3"><span id="lesson_name"></span></div>
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
  <div class="col-12 p-3 font-weight-bold">
    ご希望の科目
  </div>
  <div class="col-12">
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
              @foreach($attributes['charge_subject_level'] as $index => $name)
                @if($loop->index == 0)
                  @continue
                @elseif($loop->index >= 3)
                  @break
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
            @foreach($attributes['charge_subject_level'] as $index => $name)
              @if($loop->index == 0)
                @continue
              @elseif($loop->index >= 3)
                @break
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
  <div class="col-6 p-3 font-weight-bold english_confirm" >ご希望の英会話講師</div>
  <div class="col-6 p-3 english_confirm"><span id="english_teacher_name"></span></div>
  <div class="col-6 p-3 font-weight-bold piano_confirm" >ピアノのご経験について</div>
  <div class="col-6 p-3 piano_confirm"><span id="piano_level_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご要望について</div>
  <div class="col-6 p-3"><span id="remark"></span></div>
</div>
@endsection
