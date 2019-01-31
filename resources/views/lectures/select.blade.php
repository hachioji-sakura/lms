<div class="col-12" id="select_lesson_form">
  <div class="form-group">
    <label for="lesson" class="w-100">
      レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="lesson" class="form-control" placeholder="レッスン" required="true" onChange="select_lesson_change(this)">
      <option value="">(選択してください)</option>
    </select>
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="course" class="w-100">
      コース
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="course" class="form-control" placeholder="コース" required="true" onChange="select_course_change(this)">
      <option value="">(選択してください)</option>
    </select>
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="subject" class="w-100">
      科目
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="subject" class="form-control" placeholder="科目" required="true" >
    </select>
  </div>
</div>
<script>
var lessons = null;
var courses = null;
var _defaultOption = '<option value="">(選択してください)</option>';
function select_lesson_set(){
  if(util.isEmpty(lessons) || util.isEmpty(courses)){
    get_lectures();
    return;
  }
  console.log("select_lesson_set");

  $("select[name='lesson']").html('');
  $("select[name='course']").html(_defaultOption);
  $("select[name='subject']").html(_defaultOption);
  var c = 0;
  $.each(lessons, function(id, val){
    var _option = '<option value="'+id+'">'+val["name"]+'</option>';
    $("select[name='lesson']").append(_option);
    c++;
  });
  $("select[name='lesson']").trigger('change');
  if(c==1) $("#select_lesson_form").css("display", "none");
}
function select_lesson_change(obj){
  if(util.isEmpty(lessons)) return;
  if(util.isEmpty(courses)) return;
  var _val = $(obj).val();
  if(util.isEmpty(_val)) return ;
  var _items = lessons[_val]['courses'];
  $("select[name='course']").html(_defaultOption);
  $("select[name='subject']").html(_defaultOption);
  $.each(_items, function(id, name){
    var _option = '<option value="'+id+'">'+name+'</option>';
    $("select[name='course']").append(_option);
  });
}
function select_course_change(obj){
  if(util.isEmpty(lessons)) return;
  if(util.isEmpty(courses)) return;
  var _val = $(obj).val();
  if(util.isEmpty(_val)) return ;
  $("select[name='subject']").html(_defaultOption);
  var _items = courses[_val]['subjects'];
  $.each(_items, function(id, name){
    var _option = '<option value="'+id+'">'+name+'</option>';
    $("select[name='subject']").append(_option);
  });
}
function get_lectures(){
  console.log("get_lectures");

  lessons = util.getLocalData('lessons');
  courses = util.getLocalData('courses');

  if(!util.isEmpty(lessons) && !util.isEmpty(courses)){
    select_lesson_set();
    return;
  }
  service.getAjax(false, '/api_lectures', null,
    function(result, st, xhr) {
      var lessons = {};
      var courses = {};
      if(result['status']===200){
        $.each(result['data'], function(index, value) {
          var l = value['lesson'];
          var c = value['course'];
          var s = value['subject'];
          if(!lessons[l]){
            lessons[l] = {
              "courses" : {},
              "name" : value['lesson_name']
            };
          }
          lessons[l]["courses"][c] = value['course_name'];
          if(!courses[c]){
            courses[c] = {
              "subjects" : {},
              "name" : value['course_name']
            };
          }
          courses[c]["subjects"][s] = value['subject_name'];

        });
        util.setLocalData('lessons', lessons);
        util.setLocalData('courses', courses);
        select_lesson_set();
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー"+messageParam);
    }
  );
}
$(function(){
  select_lesson_set();
});

</script>
