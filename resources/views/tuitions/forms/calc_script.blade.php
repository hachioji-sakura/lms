<script>
function calc_tuition(){
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
  grade = grade.substring(0,1);
  /*
  $("select[name=calendar_setting_id] option").each(function(index, element){
    $(element).attr("lesson");
  });
  */
  var r = get_tuition(data["lesson"]|0, data["course_type"], grade, is_juken|0, lesson_week_count|0, data["subject"], data["course_minutes"]|0);
  $("input[name=tuition]").val(r);
}
function get_tuition(lesson, course_type, grade, is_juken, lesson_week_count, subject, minutes){
  var _base = 0;
  console.log("lesson="+lesson);
  console.log("course_type="+course_type);
  console.log("grade="+grade);
  console.log("is_juken="+is_juken);
  console.log("lesson_week_count="+lesson_week_count);
  console.log("subject="+subject);
  console.log("minutes="+minutes);
  switch(lesson){
    case 1:
      if(course_type=="group") return 0;
      if(grade=="t") return 0;
      if(grade=="a") return 0;
      if(grade=="u") return 0;
      if(minutes==30) return 0;
      _base=5000;
      if(minutes==90){
        _base-=250;
      }
      else if(minutes==120){
        _base-=500;
      }
      if(lesson_week_count>2){
        _base-=750;
      }
      else if(lesson_week_count==2) {
        _base-=500;
      }

      if(grade=='e'){
        if(lesson==1 && is_juken==1){
          _base+=1000;
        }
      }
      else if(grade=='j'){
        _base+=500;
      }
      else if(grade=='h'){
        _base+=1000;
      }
      break;
    case 2:
      if(minutes>60) return 0;
      if(subject=="chinese" && minutes!=60) return 0;
      if(subject=="chinese" && course_type=="group") return 0;
      if(course_type=="group"){
        _base= 3490;
        if(minutes==30){
          _base=1990*2;
        }
      }
      else if(course_type=="single"){
        _base= 6490;
        if(subject=="chinese"){
          //中国語
          _base=4990;
        }
        if(minutes==30){
          _base=3690*2;
        }

        if(lesson_week_count>1){
          if(minutes==30){
            //週２・30分授業業
            _base=3490*2;
          }
          else {
            //週２・1時間授業
            _base-=500;
          }
        }
      }
      break;
    case 3:
      _base=4500;
      break;
    case 4:
      if(grade!="e") return 0;
      if(minutes!=60) return 0;
      if(subject=="dance"){
        _base=1000;
      }
      else if(subject=="abacus"){
        _base=1500;
        for(var i=1;i<lesson_week_count;i++){
          _base-=250;
        }
      }
      if(course_type=="single"){
        //一人の場合、60分ではなく、40分扱いとなるため
        _base*=1.5;
      }
      break;
  }
  return _base;
}
</script>
