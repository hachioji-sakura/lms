<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  <?php
  $student = null;
  if(isset($item->student)) $student = $item->student;
   ?>
  @component('students.forms.name', [ 'prefix' => 'student_', 'is_label' => $_edit, 'item' => $student]) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student_', 'is_label' => $_edit, 'item' => $student]) @endcomponent
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_birthday', ['prefix'=>'', 'is_label' => $_edit, 'item' => $student])
    @endcomponent
  </div>
  <div class="col-12 col-md-6 mb-1">
    @component('components.select_gender', ['prefix'=>'', 'is_label' => $_edit, 'item' => $student]) @endcomponent
  </div>
  @component('students.forms.school', [ 'prefix'=>'','attributes' => $attributes, 'is_label' => $_edit, 'item' => $student]) @endcomponent
</div>
