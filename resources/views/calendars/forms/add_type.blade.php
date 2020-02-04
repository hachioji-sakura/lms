{{-- グループレッスンの場合は、生徒追加と新規追加を選べる --}}
@if(
  isset($item["tagdata"]) &&
  (
   ($item->has_tag('lesson', 2)==true && isset($item["tagdata"]['english_talk_course_type']) && isset($item["tagdata"]['english_talk_course_type']['group']))
    || ($item->has_tag('lesson', 4)==true && isset($item["tagdata"]['kids_lesson_course_type']) && isset($item["tagdata"]['kids_lesson_course_type']['group']))
  )
)
<div class="col-12 mt-2 couse_type_group">
  <div class="form-group">
    <label for="action" class="w-100">
      追加方法
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-green" type="radio" name="action" id="action_new" value="new" required="true" onchange="action_change()" checked>
      <label class="form-check-label mr-3" for="action_new">
        新規の授業予定を追加
      </label>
      <input class="form-check-input icheck flat-green" type="radio" name="action" id="action_add" value="add" required="true" onchange="action_change()">
      <label class="form-check-label mr-3" for="action_add">
        既存の予定に生徒を追加
      </label>
    </div>
  </div>
</div>
<script>
$(function(){
  action_change();
});
function action_change(){
  var a = $('input[name="action"]:checked').val();
  if(!a) return ;
  $('#new_row').remove();
  $("input.lesson_week_datetime").val("");
  $(".action_form").hide();
  if(a=='add'){
    $(".action_add").show();
  }
  else {
    $(".action_new").show();
  }
  if($('input[name=trial_date_hope]:checked').length>0){
    trial_date_hope_change();
  }
}
</script>
@else
<input type="hidden" name="action" value="new">
<script>
$(function(){
  action_change();
});
function action_change(){
  $(".action_form").hide();
  $(".action_new").show();
}
</script>
@endif
