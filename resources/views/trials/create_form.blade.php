@section('trial_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  @component('students.forms.lesson', ['attributes' => $attributes]) @endcomponent
  @component('trials.forms.trial_date', ['attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('student_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'student_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', ['prefix'=>'']) @endcomponent
  </div>
  @component('students.forms.school', [ 'prefix'=>'','attributes' => $attributes]) @endcomponent
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" role="button" class="float-right mr-1" onClick="$('.student2').collapse('toggle');">
      <i class="fa fa-chevron-down mr-1"></i>
      ご兄弟（姉妹）でのお申し込みの方
    </a>
  </div>
</div>
<div class="row collapse student2">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報(2)
  </div>
  @component('students.forms.name', [ 'prefix' => 'student2_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student2_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', ['prefix' => 'student2_']) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student2_', 'noscript'=>true, 'attributes' => $attributes]) @endcomponent
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" role="button" class="float-right mr-1" onClick="$('.student3').collapse('toggle');">
      <i class="fa fa-chevron-down mr-1"></i>
      3人兄弟（姉妹）でのお申し込みの方
    </a>
  </div>
</div>
<div class="row collapse student3">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報(3)
  </div>
  @component('students.forms.name', [ 'prefix' => 'student3_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student3_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', ['prefix' => 'student3_']) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student3_', 'noscript'=>true, 'attributes' => $attributes]) @endcomponent
</div>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-phone-square mr-1"></i>
    ご連絡先
  </div>
  @component('students.forms.email', []) @endcomponent
  @component('students.forms.phoneno', []) @endcomponent
  @component('students.forms.address', []) @endcomponent
</div>
@endsection


@section('lesson_week_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-calendar-alt mr-1"></i>
    通塾スケジュールにつきまして
  </div>
  @component('students.forms.lesson_week_count', ['_edit'=>$_edit, 'item'  => [], 'attributes' => $attributes]) @endcomponent
  @component('students.forms.course_minutes', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'  => [], 'prefix' => 'lesson', 'attributes' => $attributes, 'title' => 'ご希望の通塾曜日・時間帯']) @endcomponent
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'item'  => [], 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('subject_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4 subject_form">
    <i class="fa fa-pen-square mr-1"></i>
    塾の内容につきまして
  </div>
  @component('students.forms.subject', ['_edit'=>$_edit,'attributes' => $attributes,  '_teacher' => false, 'category_display' => false, 'grade_display' => false]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 english_talk_form">
    <i class="fa fa-comments mr-1"></i>
    英会話の授業内容につきまして
  </div>
  @component('students.forms.english_teacher', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.english_talk_lesson', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.course_type', ['prefix'=>'english_talk', 'attributes' => $attributes]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 piano_form">
    <i class="fa fa-music mr-1"></i>
    ピアノの授業内容につきまして
  </div>
  @component('students.forms.piano_level', ['attributes' => $attributes]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 kids_lesson_form">
    <i class="fa fa-shapes mr-1"></i>
    習い事の授業内容につきまして
  </div>
  @component('students.forms.kids_lesson', ['attributes' => $attributes]) @endcomponent
  @component('students.forms.course_type', ['prefix'=>'kids_lesson', 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('survey_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-question-circle mr-1"></i>
    サービス向上のためアンケートをご記入ください
  </div>
  @component('students.forms.remark', ['attributes' => $attributes]) @endcomponent
  @if(!isset($user->role))
    @component('students.forms.howto', ['attributes' => $attributes]) @endcomponent
  @endif
</div>
@endsection
