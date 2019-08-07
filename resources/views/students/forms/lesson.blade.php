<div class="col-12">
  <div class="form-group">
    <label for="lesson" class="w-100">
      @isset($title)
      {{$title}}
      @else
      ご希望のレッスン
      @endisset
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['lesson'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="lesson[]" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag('lesson', $index)===true)
      checked
      @endif
      onChange="lesson_checkbox_change(this)">{{$name}}
    </label>
    @endforeach
  </div>
</div>
<script>
function lesson_checkbox_change(obj){
  console.log("lesson_checkbox_change");
  var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
  var is_english = $('input[type="checkbox"][name="lesson[]"][value="2"]').prop("checked");
  var is_piano = $('input[type="checkbox"][name="lesson[]"][value="3"]').prop("checked");
  var is_kids_lesson = $('input[type="checkbox"][name="lesson[]"][value="4"]').prop("checked");
  if(is_school){
    $(".subject_form").show();
    $(".subject_confirm").show();
  }
  else {
    $(".subject_form").hide();
    $(".subject_confirm").hide();
  }
  if(is_english){
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
  if(is_piano){
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
  if(is_kids_lesson){
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
  lesson_place_filter();
  course_minutes_filter();
  //grade_select_change();
}
function lesson_place_filter(){
  var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
  var is_english = $('input[type="checkbox"][name="lesson[]"][value="2"]').prop("checked");
  var is_piano = $('input[type="checkbox"][name="lesson[]"][value="3"]').prop("checked");
  var is_kids_lesson = $('input[type="checkbox"][name="lesson[]"][value="4"]').prop("checked");
  $("label.lesson_place").show();
  if(!is_school && !is_english){
    //ピアノ＝子安、
    $("label.lesson_place:contains('八王子北口校')").hide();
    $("label.lesson_place:contains('国立校')").hide();
    $("label.lesson_place:contains('日野市豊田校')").hide();
    $("label.lesson_place:contains('アローレ校')").hide();
    $("label.lesson_place:contains('ダットッチ校')").hide();
    if(!is_kids_lesson){
      $("label.lesson_place:contains('八王子南口校')").hide();
    }
  }
  else if(!is_school && is_english){
    $("label.lesson_place:contains('アローレ校')").hide();
  }
  else if(is_school && !is_english){
    $("label.lesson_place:contains('ダットッチ校')").hide();
  }
}
function course_minutes_filter(){
  var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
  var is_english = $('input[type="checkbox"][name="lesson[]"][value="2"]').prop("checked");
  var is_piano = $('input[type="checkbox"][name="lesson[]"][value="3"]').prop("checked");
  var is_kids_lesson = $('input[type="checkbox"][name="lesson[]"][value="4"]').prop("checked");
  $("label.course_minutes").show();
  if(!is_school){
    //塾以外＝90分、120分なし
    $("label.course_minutes:contains('９０分')").hide();
    $("label.course_minutes:contains('１２０分')").hide();
  }
  if(!is_piano && !is_english && !is_kids_lesson){
    //習い事がない
    $("label.course_minutes:contains('３０分')").hide();
  }
}
</script>
