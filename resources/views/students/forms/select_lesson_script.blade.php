<script>
function lesson_checkbox_change(obj){
  var name = $(obj).attr('name');
  console.log("lesson_checkbox_change");
  var check_lesson = get_lesson_check(name);
  if(check_lesson["is_school"]){
    $(".subject_form").show();
    $(".subject_confirm").show();
  }
  else {
    $(".subject_form").hide();
    $(".subject_confirm").hide();
  }
  if(check_lesson["is_english"]){
    $(".english_talk_form").show();
    $(".english_talk_form input").show();
    $(".english_talk_form select").show();
    $(".english_talk_confirm").show();
  }
  else {
    $(".english_talk_form").hide();
    $(".english_talk_form input").hide();
    $(".english_talk_form select").hide();
    $(".english_talk_confirm").hide();
  }
  if(check_lesson["is_piano"]){
    $(".piano_form").show();
    $(".piano_form input").show();
    $(".piano_form select").show();
    $(".piano_confirm").show();
  }
  else {
    $(".piano_form").hide();
    $(".piano_form input").hide();
    $(".piano_form select").hide();
    $(".piano_confirm").hide();
  }
  if(check_lesson["is_kids_lesson"]){
    $(".kids_lesson_form").show();
    $(".kids_lesson_form input").show();
    $(".kids_lesson_form select").show();
    $(".kids_lesson_confirm").show();
  }
  else {
    $(".kids_lesson_form").hide();
    $(".kids_lesson_form input").hide();
    $(".kids_lesson_form select").hide();
    $(".kids_lesson_confirm").hide();
  }
  lesson_place_filter(name);
  course_minutes_filter(name);
  //grade_select_change();
}
function lesson_place_filter(name){
  var check_lesson = get_lesson_check(name);
  $("label.lesson_place").show();
  $("label.lesson_place:contains('ダットッチ校')").hide();
  if(!check_lesson["is_school"] && !check_lesson["is_english"]){
    //ピアノ＝子安、
    $("label.lesson_place:contains('八王子北口校')").hide();
    $("label.lesson_place:contains('国立校')").hide();
    $("label.lesson_place:contains('日野市豊田校')").hide();
    $("label.lesson_place:contains('アローレ校')").hide();
    if(!check_lesson["is_kids_lesson"]){
      $("label.lesson_place:contains('八王子南口校')").hide();
    }
  }
  else if(!check_lesson["is_school"] && check_lesson["is_english"]){
    $("label.lesson_place:contains('アローレ校')").hide();
  }
}
function course_minutes_filter(name){
  console.log("course_minutes_filter("+name+")");
  var check_lesson = get_lesson_check(name);
  $("label.course_minutes").show();
  if(!check_lesson["is_school"]){
    //塾以外＝90分、120分なし
    $("label.course_minutes:contains('９０分')").hide();
    $("label.course_minutes:contains('１２０分')").hide();
  }
  if(!check_lesson["is_piano"] && !check_lesson["is_english"] && !check_lesson["is_kids_lesson"]){
    //習い事がない
    $("label.course_minutes:contains('３０分')").hide();
  }
}
function get_lesson_check(name){
  var is_school = false;
  var is_english = false;
  var is_piano = false;
  var is_kids_lesson = false;
  if($('input[name="'+name+'"][type="checkbox"]').length > 0){
    is_school = $('input[name="'+name+'"][value="1"]').prop("checked");
    is_english = $('input[name="'+name+'"][value="2"]').prop("checked");
    is_piano = $('input[name="'+name+'"][value="3"]').prop("checked");
    is_kids_lesson = $('input[name="'+name+'"][value="4"]').prop("checked");
  }
  else if($('input[name="'+name+'"][type="hidden"]').length > 0){
    var _lesson = $('input[name="'+name+'"][type="hidden"]').val();
    if(_lesson == 1) is_school = true;
    else if(_lesson == 2) is_english = true;
    else if(_lesson == 3) is_piano = true;
    else if(_lesson == 4) is_kids_lesson = true;
  }
  return {
    'is_school' : is_school,
    'is_english' : is_english,
    'is_piano' : is_piano,
    'is_kids_lesson' : is_kids_lesson
  };
}
</script>
