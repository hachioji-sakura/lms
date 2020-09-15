<script>
function calc_tuition(){
  console.log('calc_tuition');
  var select_id = $("select[name=calendar_setting_id] option:selected");
  var fields = ['lesson', 'course_type', 'subject', 'teacher_id', 'course_minutes'];
  var data = [];
  if(select_id.val()>0){
    for(var i=0;i<fields.length;i++){
      $('input[name='+fields[i]+']').val(select_id.attr(fields[i]));
      data[fields[i]] = select_id.attr(fields[i]);
    }
    $("input[name=title]").val(select_id.text().trim());
  }
  else {
    for(var i=0;i<fields.length;i++){
      data[fields[i]] = $('input[name='+fields[i]+']').val();
    }
  }
  var grade = $("input[name=grade]").val();
  var is_juken = $("input[name=is_juken]").val();
  var lesson_week_count = $("input[name=lesson_week_count]").val();
  var r = get_tuition(data["lesson"]|0, data["course_type"], grade, is_juken|0, lesson_week_count|0, data["subject"], data["course_minutes"]|0, data["tacher_id"]);
  $("input[id=tuition]").val(r);
}
function get_tuition(lesson, course, grade, is_juken, lesson_week_count, subject, course_minutes, teacher_id){
  var url = '/api_tuition';
  var data = {
    "lesson" : lesson,
    "course" : course,
    "course_minutes" : course_minutes,
    "grade" : grade,
    "lesson_week_count" : lesson_week_count,
    "is_juken" : is_juken,
    "subject" : subject,
    "teacher_id" : teacher_id,
  };
  var lesson_fee = null;
  service.getAjax(false, url, data,
    function(result, st, xhr) {
      if(result['status']===200){
        lesson_fee = result['data']['lesson_fee'];
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー(calc_script.get_tuition)");
    }
  );
  console.log("get_tuition:"+lesson_fee);
  return lesson_fee;
}
</script>
