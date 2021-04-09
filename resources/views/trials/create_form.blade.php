@section('trial_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  @component('students.forms.lesson', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
  <?php
    $is_label = $_edit;
    if(isset($user) && $user->role=='manager') $is_label = false;
  ?>
  @component('trials.forms.trial_date', ['_edit'=>$_edit, 'is_label'=>$is_label, 'item'=>$item,'attributes' => $attributes]) @endcomponent
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('trials.forms.parent_interview', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('trial_form_v2')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業お申込み内容
  </div>
  @component('students.forms.lesson', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
  <?php
    $is_label = false;
  ?>
  @component('trials.forms.trial_date', ['_edit'=>$_edit, 'is_label'=>$is_label, 'item'=>$item,'attributes' => $attributes]) @endcomponent
  @component('students.forms.lesson_place', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('trials.forms.parent_interview', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('candidate_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    体験授業希望日時変更のお願い
  </div>
  @component('trials.forms.trial_date', ['_edit'=>$_edit, 'is_label'=>false, 'item'=>$item,'attributes' => $attributes]) @endcomponent
</div>
@endsection


@section('student_form')
@component('trials.forms.entry_students', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-phone-square mr-1"></i>
    ご連絡先
  </div>
  <?php
    $parent = null;
    if($_edit==true){
      $parent = $item->parent->details();
    }
  ?>
  @component('students.forms.name', [ 'prefix' => 'parent_', 'is_label' => $_edit, 'item' => $parent]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'parent_', 'is_label' => $_edit, 'item' => $parent]) @endcomponent
  @component('students.forms.email', ['is_label'=>$_edit, 'item'=>$parent]) @endcomponent
  @component('students.forms.phoneno', ['is_label'=>$_edit, 'item'=>$parent]) @endcomponent
  @component('students.forms.address', ['is_label'=>$_edit, 'item'=>$parent]) @endcomponent
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
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4 subject_form ">
    <i class="fa fa-pen-square mr-1"></i>
    塾の内容につきまして
  </div>
  @component('students.forms.subject', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => false, 'category_display' => false, 'grade_display' => false]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 english_talk_form ">
    <i class="fa fa-comments mr-1"></i>
    英会話の授業内容につきまして
  </div>
  @component('students.forms.english_teacher', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.english_talk_lesson', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'prefix'=>'english_talk', 'attributes' => $attributes]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 piano_form ">
    <i class="fa fa-music mr-1"></i>
    ピアノの授業内容につきまして
  </div>
  @component('students.forms.piano_level', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  <div class="col-12 bg-info p-2 pl-4 mb-4 kids_lesson_form ">
    <i class="fa fa-shapes mr-1"></i>
    習い事の授業内容につきまして
  </div>
  @component('students.forms.kids_lesson', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  {{--
  @component('students.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'prefix'=>'kids_lesson', 'attributes' => $attributes]) @endcomponent
  --}}
</div>
@endsection

@section('survey_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-question-circle mr-1"></i>
    サービス向上のためアンケートをご記入ください
  </div>
  @component('students.forms.entry_milestone', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.remark', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @if(!isset($user->role))
  @component('students.forms.howto', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
  @endif
</div>
@endsection
