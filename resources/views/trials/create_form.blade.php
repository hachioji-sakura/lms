@section('student_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'student_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', []) @endcomponent
  </div>
  @component('students.forms.school', [ 'attributes' => $attributes]) @endcomponent
  @component('students.forms.email', []) @endcomponent
</div>
@endsection

@section('survey_form')
<div class="row">
  @component('students.forms.remark', ['attributes' => $attributes]) @endcomponent
  @if(!isset($user->role))
    @component('students.forms.howto', ['attributes' => $attributes]) @endcomponent
  @endif
</div>
@endsection

@section('trial_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  @component('students.forms.lesson', ['attributes' => $attributes]) @endcomponent

  <div class="row form-group p-2">
    <div class="col-12 mt-2 col-md-4">
        <label for="start_date" class="w-100">
          第１希望日
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <input type="text" name="trial_date1" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}">
    </div>
    <div class="col-12 mt-2 col-md-8">
      <label for="start_date" class="w-100">
        時間帯
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="trial_start_time1" class="form-control float-left mr-1 w-40" required="true">
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <div class="w-10 text-center float-left mx-2">～</div>
      <select name="trial_end_time1" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time1" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time1" not_equal_error="時間帯範囲が間違っています" >
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
    </div>
  </div>
  <div class="row form-group p-2">
    <div class="col-12 mt-2 col-md-4">
        <label for="start_date" class="w-100">
          第２希望日
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <input type="text" name="trial_date2" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}">
    </div>
    <div class="col-12 mt-2 col-md-8">
      <label for="start_date" class="w-100">
        時間帯
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="trial_start_time2" class="form-control float-left mr-1 w-40" required="true">
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
      <div class="w-10 text-center float-left mx-2">～</div>
      <select name="trial_end_time2" class="form-control float-left mr-1 w-40" required="true" greater="trial_start_time2" greater_error="時間帯範囲が間違っています" not_equal="trial_start_time2" not_equal_error="時間帯範囲が間違っています" >
        <option value="">(選択してください)</option>
        @for ($i = 8; $i < 23; $i++)
          <option value="{{$i}}" >{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
        @endfor
      </select>
    </div>
  </div>
  @component('students.forms.lesson_place', ['attributes' => $attributes]) @endcomponent

</div>
@endsection
@section('subject_form')
<div class="row">
  @component('students.forms.subject', ['attributes' => $attributes, 'category_display' => false, 'grade_display' => false]) @endcomponent
  @component('students.forms.english_teacher', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.piano_level', ['attributes' => $attributes]) @endcomponent
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
      <rb><span id="student_name_last"></span>&nbsp;<span id="student_name_first"></span></rb>
      <!-- rt><span id="student_kana_last"></span>&nbsp;<span id="student_kana_first"></span></rt -->
    </ruby>
  </div>
  <div class="col-6 p-3 font-weight-bold" >性別</div>
  <div class="col-6 p-3"><span id="gender_name"></span></div>
  {{--
    <div class="col-6 p-3 font-weight-bold" >生年月日</div>
    <div class="col-6 p-3"><span id="birth_day"></span></div>
  --}}
  <div class="col-6 p-3 font-weight-bold" >学年</div>
  <div class="col-6 p-3"><span id="grade_name"></span></div>
  <div class="col-6 p-3 font-weight-bold school_name_confirm" >学校名</div>
  <div class="col-6 p-3 school_name_confirm"><span id="school_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >メールアドレス</div>
  <div class="col-6 p-3"><span id="email"></span></div>
</div>
{{--
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
  <div class="col-6 p-3 font-weight-bold" >ご連絡先</div>
  <div class="col-6 p-3"><span id="phone_no"></span></div>
</div>
--}}
<div class="row">
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  <div class="col-6 p-3 font-weight-bold" >第１希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time1"></span></div>
  <div class="col-6 p-3 font-weight-bold" >第２希望日時</div>
  <div class="col-6 p-3"><span id="trial_date_time2"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望の校舎</div>
  <div class="col-6 p-3"><span id="lesson_place_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご希望のレッスン</div>
  <div class="col-6 p-3"><span id="lesson_name"></span></div>
  <div class="col-12 bg-info p-2 pl-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    授業に関するご要望について
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
  <div class="col-6 p-3 font-weight-bold english_confirm" >ご希望の英会話講師</div>
  <div class="col-6 p-3 english_confirm"><span id="english_teacher_name"></span></div>
  <div class="col-6 p-3 font-weight-bold piano_confirm" >ピアノのご経験について</div>
  <div class="col-6 p-3 piano_confirm"><span id="piano_level_name"></span></div>
  <div class="col-6 p-3 font-weight-bold" >ご要望について</div>
  <div class="col-6 p-3"><span id="remark"></span></div>
  <div class="col-6 p-3 font-weight-bold" >当塾をお知りになった方法は何でしょうか？</div>
  <div class="col-6 p-3"><span id="howto_name"></span></div>
  <div class="col-6 p-3 font-weight-bold howto_word_confirm" >検索ワードをお答えください</div>
  <div class="col-6 p-3 howto_word_confirm"><span id="howto_word"></span></div>
</div>
@endsection


{{--
@section('parent_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    お客様情報
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_name_last">
        氏
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_name_last" name="parent_name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_name_first">
        名
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_name_first" name="parent_name_first" class="form-control" placeholder="例：桜" required="true" inputtype="zenkaku">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_kana_last">
        氏（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_kana_last" name="parent_kana_last" class="form-control" placeholder="例：ハチオウジ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="parent_kana_first">
        名（カナ）
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="parent_kana_first" name="parent_kana_first" class="form-control" placeholder="例：サクラ" required="true" inputtype="zenkakukana">
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="email">
        メールアドレス
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email">
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <label for="phone_no">
        連絡先
        <span class="right badge badge-secondary ml-1">任意</span>
        <span class="text-sm">ハイフン(-)不要</span>
      </label>
      <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222"  inputtype="number">
    </div>
  </div>
</div>
@endsection
--}}
