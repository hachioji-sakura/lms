@section('first_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-clock mr-1"></i>
    基本情報
  </div>
  @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
  @component('calendars.forms.select_lesson', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
  @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
  @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent
</div>
@endsection
@section('second_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒情報
  </div>
  @component('calendars.forms.course_type', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'),'attributes' => $attributes]); @endcomponent
  @component('calendars.forms.select_student_group', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
  @component('calendars.forms.select_student', ['_edit' => $_edit, 'item'=>$item]); @endcomponent
</div>
@endsection
@section('third_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-chalkboard-teacher mr-1"></i>
    授業情報
  </div>
  @component('calendars.forms.select_exchanged_calendar', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]);  @endcomponent
  @component('calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$teachers[0]->user->details('teachers'), 'attributes' => $attributes]); @endcomponent
</div>
@endsection
@section('confirm_form')
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-file-invoice mr-1"></i>
    登録内容確認
  </div>
    <?php
      $form_data = ["teacher_name" => "講師",
                    "start_time"=>"開始日時",
                    "place_name"=>"場所",
                    "course_minutes_name"=>"授業時間",
                    "course_type_name"=>"コース",
                    "student_name"=>"生徒",
                    "subject_name" => "科目"];
    ?>
    @foreach($form_data as $key => $name)
    <div class="col-6 p-3 font-weight-bold" >{{$name}}</div>
    <div class="col-6 p-3"><span id="{{$key}}"></span></div>
    @endforeach
</div>
@endsection
