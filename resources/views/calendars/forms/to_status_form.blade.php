@if($item->is_group()==true || $item->trial_id>0)
<input type="hidden" name="status" value="confirm" />
@else
<div class="col-12 mt-2 couse_type_group">
  <div class="form-group">
    <label for="to_status" class="w-100">
      {{__('labels.updated_status')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-grey" type="radio" name="status" id="to_status_fix" value="fix" required="true" checked>
      <label class="form-check-label mr-3" for="to_status_fix" checked>
        {{__('labels.not_require_student_confirm')}}
      </label>
      <input class="form-check-input icheck flat-red" type="radio" name="status" id="to_status_confirm" value="confirm" required="true" >
      <label class="form-check-label mr-3" for="to_status_confirm">
        {{__('labels.require_student_confirm')}}
      </label>
    </div>
  </div>
</div>
@endif
