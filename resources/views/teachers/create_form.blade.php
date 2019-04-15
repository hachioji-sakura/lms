@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </h5>
  </div>
  @component('students.forms.email', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @component('students.forms.password', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
</div>
@endsection



@section('item_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      {{$domain_name}}情報
    </h5>
  </div>
  @component('students.forms.name', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  @component('students.forms.kana', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  <div class="col-12 mb-2">
    @component('components.select_birthday', ['_edit'=>$_edit, 'item' => $item])
    @endcomponent
  </div>
  <div class="col-12 mb-2">
    @component('components.select_gender', ['_edit'=>$_edit, 'item' => $item])
    @endcomponent
  </div>
  @component('students.forms.phoneno', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'',]) @endcomponent
</div>
@endsection

@section('lesson_week_form')
<div class="row">
  @component('students.forms.lesson', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'', 'title'=>'担当レッスン']) @endcomponent
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item, 'prefix'=> 'lesson', 'attributes' => $attributes, 'title' => '担当可能な曜日・時間帯']) @endcomponent
</div>
@endsection

@section('subject_form')
<div class="row">
  @component('students.forms.subject', ['_edit'=>$_edit, 'item'=>$item, '_teacher' => true, 'attributes' => $attributes, 'category_display' => false, 'grade_display' => true]) @endcomponent
  @component('students.forms.english_teacher', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.english_talk_lesson', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.piano_level', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.kids_lesson', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
</div>
@endsection

@section('tag_form')
<div class="row">
  @component('students.forms.teacher_character', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
</div>
@endsection
