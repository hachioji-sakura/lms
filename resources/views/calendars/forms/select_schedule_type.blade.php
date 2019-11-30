<div class="col-6 mt-2">
  <div class="form-group">
    <label for="schedule_type" class="w-100">
      {{__('labels.schedule_type')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="form-check ml-2" id="schedule_type_form_group">
          <input class="form-check-input icheck flat-green" type="radio" name="schedule_type" id="schedule_type_class" value="class" required="true" onChange="schedule_type_change()"
          @if(isset($_edit) && $_edit==true && $item->is_management()==false)
            checked
          @else(isset($_edit) && $_edit==false)
            checked
          @endif
          >
          <label class="form-check-label" for="schedule_type_class">
            {{__('labels.school_lesson')}}
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="schedule_type" id="schedule_type_other" value="other" required="true" onChange="schedule_type_change()"
          @if(isset($_edit) && $_edit==true && $item->is_management()==true)
            checked
          @endif
          >
          <label class="form-check-label" for="schedule_type_other">
            {{__('labels.interview_etc')}}
          </label>
      </div>
    </div>
  </div>
</div>
<script>
$(function(){
  schedule_type_change();
});
function schedule_type_change(){
  var schedule_type = $("input[name='schedule_type']:checked").val();
  console.log('schedule_type_change:'+schedule_type);
  $(".schedule_type").hide();
  if(schedule_type == "class"){
    $(".schedule_type_class").show();
  }
  else if(schedule_type == "other"){
    $(".schedule_type_other").show();
  }
}
</script>
