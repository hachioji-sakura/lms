<div class="col-5 collapse student_selected" id="select_lesson_form">
  <div class="form-group">
    <label for="lesson" class="w-100">
      レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson" class="form-control" placeholder="レッスン" required="true" onChange="select_course_set();">
      <option value="">(選択してください)</option>
    </select>
  </div>
</div>
<div class="col-7 collapse student_selected" id="select_course_form">
  <div class="form-group">
    <label for="course" class="w-100">
      コース
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="course" class="form-control" placeholder="コース" required="true" onChange="select_subject_set();">
      <option value="">(選択してください)</option>
      @foreach($attributes['course'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
    <input type="hidden" name="lecture_id" value="">
  </div>
</div>
<div class="col-12 collapse student_selected">
  <div class="form-group">
    <label for="subject" class="w-100">
      科目
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="subject" class="form-control" placeholder="科目" required="true" onChange="select_lecture_id_set();">
      <option value="">(選択してください)</option>
      @foreach($attributes['subject'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<script>
$(function(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  if(teacher_id > 0){
    get_lectures(teacher_id, 0);
  }
  $('select[name=student_id]').on('change', function(e){
    var student_id =$(this).val();
    var teacher_id = $('*[name=teacher_id]').val();
    get_lectures(teacher_id, student_id);

  });
  $('select[name=teacher_id]').on('change', function(e){
    var teacher_id =$(this).val();
    var student_id = $('select[name=student_id]').val();
    get_lectures(teacher_id, student_id);
  });
});
var lectures = {};
function get_lectures(teacher_id, student_id){
  console.log("get_lectures");
  if(student_id > 0){
    $(".student_selected").collapse("show");
  }
  else {
    $(".student_selected").collapse("hide");
  }
  service.getAjax(false, '/api_lectures?teacher_id='+teacher_id+'&student_id='+student_id, null,
    function(result, st, xhr) {
      if(result['status']===200){
        lectures = result['data'];
        console.log(lectures);
        select_lesson_set();
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー");
    }
  );
}
var _defaultOption = '<option value="">(選択してください)</option>';
function select_lesson_set(){
  console.log("select_lesson_set");
  var data = {};
  $.each(lectures, function(index, value) {
    var l = value['lesson'];
    data[l['attribute_value']] = l['attribute_name'];
  });
  $("select[name='lesson']").html('');
  var c = 0;
  $.each(data, function(id, val){
    var _option = '<option value="'+id+'">'+val+'</option>';
    $("select[name='lesson']").append(_option);
    c++;
  });
  $("select[name='lesson']").trigger('change');
}
function select_course_set(){
  var lesson=$('select[name="lesson"]').val();
  console.log("select_course_set:"+lesson);
  $.each(lectures, function(index, value) {
    var l = value['lesson'];
    var c = value['course'];
    if(l['attribute_value']==lesson){
      data[c['attribute_value']] = c['attribute_name'];
    }
  });
  $("select[name='course']").html('');
  var c = 0;
  $.each(data, function(id, val){
    var _option = '<option value="'+id+'">'+val+'</option>';
    $("select[name='course']").append(_option);
    c++;
  });
  $("select[name='course']").trigger('change');
}
function select_subject_set(){
  var lesson=$('select[name="lesson"]').val();
  var course=$('select[name="course"]').val();
  console.log("select_subject_set:"+lesson+'.'+course);
  data = {};
  $.each(lectures, function(index, value) {
    var l = value['lesson'];
    var c = value['course'];
    var s = value['subject'];
    if(l['attribute_value']==lesson && c['attribute_value']==course){
      data[s['attribute_value']] = s['attribute_name'];
    }
  });
  $("select[name='subject']").html('');
  var c = 0;
  $.each(data, function(id, val){
    var _option = '<option value="'+id+'">'+val+'</option>';
    $("select[name='subject']").append(_option);
    c++;
  });
  $("select[name='subject']").trigger('change');

}
function select_lecture_id_set(){
  var lesson=$('select[name="lesson"]').val();
  var course=$('select[name="course"]').val();
  var subject=$('select[name="subject"]').val();
  $.each(lectures, function(index, value) {
    var l = value['lesson'];
    var c = value['course'];
    var s = value['subject'];
    if(l['attribute_value']==lesson && c['attribute_value']==course  && s['attribute_value']==subject){
      $('input[name="lecture_id"]').val(value['id']);
      return;
    }
  });
}
</script>
