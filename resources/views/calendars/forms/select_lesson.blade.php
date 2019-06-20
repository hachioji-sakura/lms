@if(count($teacher->get_tags('lesson'))>1)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="course_type" class="w-100">
      レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      @foreach($teacher->get_tags('lesson') as $lesson)
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="lesson" id="lesson_{{$lesson["value"]}}" value="{{$lesson["value"]}}" alt="{{$lesson["name"]}}" required="true" onChange="lesson_change()"
            @if($loop->index===0)
             checked
            @endif
            ><label class="form-check-label" for="lesson_{{$lesson["value"]}}">{{$lesson["name"]}}</label>
        </div>
      @endforeach
    </div>
  </div>
</div>
@else
{{-- レッスンが１つしかない --}}
<input type="hidden" name="lesson" value="{{$teacher->get_tag('lesson')['value']}}" alt="{{$teacher->get_tag('lesson')['name']}}">
@endif
<script>
$(function(){
  lesson_change();
});
function lesson_change(){
  var lesson = ($('input[name=lesson]:checked').val())|0;
  if(lesson==0){
    lesson = ($('input[name=lesson]').val())|0;
  }
  $(".charge_subject").hide();
  $("#course_type_form .form-check").hide();
  $("#course_type_form_single").show();
  $("#course_type_form_family").show();
  $(".charge_subject_"+lesson).show();
  console.log("lesson_change:"+lesson);
  switch(lesson){
    case 2:
    case 4:
      $("#course_type_form_group").show();
      break;
  }
  $(".lesson_selected").collapse('show');
  course_type_change();
}
</script>
