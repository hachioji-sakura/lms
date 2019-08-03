<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-graduate mr-1"></i>
    生徒様情報
  </div>
  @component('students.forms.name', [ 'prefix' => 'student_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    @component('components.select_birthday', ['prefix'=>''])
    @endcomponent
  </div>
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    @component('components.select_gender', ['prefix'=>'']) @endcomponent
  </div>
  @component('students.forms.school', [ 'prefix'=>'','attributes' => $attributes]) @endcomponent
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" role="button" class="float-right mr-1" onClick="show_student_form(2);">
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
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    @component('components.select_birthday', ['prefix'=>'student2_'])
    @endcomponent
  </div>
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    @component('components.select_gender', ['prefix' => 'student2_']) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student2_', 'attributes' => $attributes]) @endcomponent
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
  @component('students.forms.name', [ 'prefix' => 'student3_']) @endcomponent
  @component('students.forms.kana', [ 'prefix' => 'student3_']) @endcomponent
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_birthday', ['prefix'=>'student2_'])
    @endcomponent
  </div>
  <div class="col-12 col-lg-6 col-md-6">
    @component('components.select_gender', ['prefix' => 'student3_']) @endcomponent
  </div>
  @component('students.forms.school', ['prefix' => 'student3_', 'attributes' => $attributes]) @endcomponent
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
