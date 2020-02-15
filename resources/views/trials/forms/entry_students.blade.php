<?php
  $student1=null;
  $student2=null;
  $student3=null;
  if($item!=[]){
    foreach($item->trial_students as $student){
      if($student1==null){
        $student1 = $student->student->details();
      }
      else if($student2==null){
        $student2 = $student->student->details();
      }
      else if($student3==null){
        $student3 = $student->student->details();
      }
    }
  }
?>
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'student_', 'is_label' => $_edit, 'item' => $student1]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student_', 'is_label' => $_edit, 'item' => $student1]) @endcomponent
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_birthday', ['prefix'=>'', 'is_label' => $_edit, 'item' => $student1])
    @endcomponent
  </div>
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_gender', ['prefix'=>'', 'is_label' => $_edit, 'item' => $student1]) @endcomponent
  </div>
  @component('students.forms.school', [ 'prefix'=>'','attributes' => $attributes, 'is_label' => $_edit, 'item' => $student1]) @endcomponent
  @if($_edit!=true)
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" role="button" class="float-right mr-1" onClick="show_student_form(2);">
      <i class="fa fa-chevron-down mr-1"></i>
      ご兄弟（姉妹）でのお申し込みの方
    </a>
  </div>
  @endif
</div>
<div class="row collapse student2">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報(2)
  </div>
  @component('students.forms.name', [ 'prefix' => 'student2_', 'is_label' => $_edit, 'item' => $student2]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student2_', 'is_label' => $_edit, 'item' => $student2]) @endcomponent
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_birthday', ['prefix'=>'student2_', 'is_label' => $_edit, 'item' => $student2])
    @endcomponent
  </div>
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_gender', ['prefix' => 'student2_', 'is_label' => $_edit, 'item' => $student2]) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student2_', 'attributes' => $attributes, 'is_label' => $_edit, 'item' => $student2]) @endcomponent
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" role="button" class="float-right mr-1" onClick="show_student_form(3);">
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
  @component('students.forms.name', [ 'prefix' => 'student3_', 'is_label' => $_edit, 'item' => $student3]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student3_', 'is_label' => $_edit, 'item' => $student3]) @endcomponent
  <div class="col-12 col-md-6">
    @component('components.select_birthday', ['prefix'=>'student2_', 'is_label' => $_edit, 'item' => $student3])
    @endcomponent
  </div>
  <div class="col-12 col-md-6">
    @component('components.select_gender', ['prefix' => 'student3_', 'is_label' => $_edit, 'item' => $student3]) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student3_', 'attributes' => $attributes, 'is_label' => $_edit, 'item' => $student3]) @endcomponent
</div>
<script>
function show_student_form(no){
  if(no===2){
    $('.student2').collapse('toggle');
    $('.student3').collapse('hide');
  }
  else if(no===3){
    $('.student3').collapse('toggle');
  }
}
</script>
