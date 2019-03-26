<div class="col-12">
  <div class="form-group">
    <label for="lesson" class="w-100">
      @isset($title)
      {{$title}}
      @else
      ご希望のレッスン
      @endisset
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['lesson'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="lesson[]" class="icheck flat-green" required="true"
      @if(isset($item) && $item->user->has_tag('lesson', $index)===true)
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
    $(".english_form").show();
    $(".english_confirm").show();
    $(".course_type_form").show();
    $(".course_type_confirm").show();
  }
  else {
    $(".english_form").hide();
    $(".english_confirm").hide();
    $(".course_type_form").hide();
    $(".course_type_confirm").hide();
  }
  if(is_piano){
    $(".piano_form").show();
    $(".piano_confirm").show();
  }
  else {
    $(".piano_form").hide();
    $(".piano_confirm").hide();
  }
  if(is_kids_lesson){
    $(".kids_lesson_form").show();
    $(".kids_lesson_confirm").show();
    $(".course_type_form").show();
    $(".course_type_confirm").show();
  }
  else {
    $(".kids_lesson_form").hide();
    $(".kids_lesson_confirm").hide();
    $(".course_type_form").hide();
    $(".course_type_confirm").hide();
  }
  //grade_select_change();
}
</script>
