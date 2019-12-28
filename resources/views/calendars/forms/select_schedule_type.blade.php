@if($item["exchanged_calendar_id"] > 0)
<input type="hidden" name="schedule_type" value="class" >
@elseif($_edit==true)
  @if($item->is_teaching()==true)
    <input type="hidden" name="schedule_type" value="class" >
  @elseif($item->work==9)
    <input type="hidden" name="schedule_type" value="office_work" >
  @else
    <input type="hidden" name="schedule_type" value="other" >
  @endif
@else
<div class="col-6 mt-2">
  <div class="form-group">
    <label for="schedule_type" class="w-100">
      {{__('labels.schedule_type')}}
      @if($_edit==true)
      <span class="right badge badge-warning ml-1">{{__('labels.disabled')}}</span>
      @else
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    <div class="input-group">
      <div class="form-check ml-2" id="schedule_type_form_group">
          <input class="form-check-input icheck flat-green" type="radio" name="schedule_type" id="schedule_type_class" value="class" required="true" onChange="schedule_type_change()"
          @if(isset($_edit) && $_edit==true && $item->is_management()==false)
            checked
          @else(isset($_edit) && $_edit==false)
            checked
          @endif

          @if($_edit==true)
            disabled
          @endif
          >
          <label class="form-check-label" for="schedule_type_class">
            {{__('labels.school_lesson')}}
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="schedule_type" id="schedule_type_other" value="other" required="true" onChange="schedule_type_change()"
          @if(isset($_edit) && $_edit==true && $item->is_management()==true && $item->work!=9)
            checked
          @endif

          @if($_edit==true)
            disabled
          @endif

          >
          <label class="form-check-label" for="schedule_type_other">
            {{__('labels.interview_etc')}}
          </label>
      </div>
      @if($_edit==false)
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="schedule_type" id="schedule_type_office_work" value="office_work" required="true" onChange="schedule_type_change()"
          @if(isset($_edit) && $_edit==true && $item->is_management()==true && $item->work==9)
            checked
          @endif

          @if($_edit==true)
            disabled
          @endif

          >
          <label class="form-check-label" for="schedule_type_office_work">
            {{__('labels.office_work')}}
          </label>
      </div>
      @endif
    </div>
  </div>
</div>
@endif
<script>
$(function(){
  schedule_type_change();
});
function schedule_type_change(){
  var schedule_type = $("input[name='schedule_type']:checked").val();
  if(!schedule_type) {
    schedule_type = $("input[name='schedule_type'][type='hidden']").val();
  }
  if(!schedule_type) return false;
  console.log('schedule_type_change:'+schedule_type);
  $(".schedule_type").hide();
  $(".schedule_type_"+schedule_type).show();
}
</script>
