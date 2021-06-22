@if($item->is_group()==true || ($item->trial_id>0 && $item->is_teaching()==true))
  <div class="col-6 mt-2">
    <div class="form-group">
      <label for="status_confirm" class="w-100">
        {{__('labels.schedule_confirm')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group" >
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status" id="status_confirm" value="confirm" required="true" onChange="select_action_change();"
               checked
        >
        <label class="form-check-label mr-3" for="status_confirm">
          {{__('labels.schedule_to_confirm')}}
        </label>
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status" id="status_cancel" value="cancel" required="true" onChange="select_action_change();"
        >
        <label class="form-check-label mr-3" for="status_cancel">
          {{__('labels.do_cancel')}}
        </label>
      </div>
    </div>
  </div>

  <div class="col-6 mt-2 collapse student_confirm_form">
    <div class="form-group">
      <label for="send_mail" class="w-100">
        {{__('labels.updated_status')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <div class="input-group" >
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status_student" id="status_fix_student" value="fix" required="true" onChange="student_confirm_change()">
        <label class="form-check-label mr-3" for="status_fix_student">
          {{__('labels.not_require_student_confirm')}}
        </label>
        <input class="form-check-input icheck flat-green" type="radio" name="dummy_status_student" id="status_confirm_student" value="confirm" required="true" onChange="student_confirm_change()">
        <label class="form-check-label mr-3" for="status_confirm_student">
          {{__('labels.require_student_confirm')}}
        </label>
      </div>
    </div>
  </div>

  <div class="row status_change_form" style="display:none;">
    <div class="col-6 mb-2">
      <label for="cancel_reason" class="w-100">
        {{__('labels.require_student_confirm')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea id="cancel_reason" name="cancel_reason" class="form-control" placeholder="" inputtype="zenkaku"></textarea>
    </div>
  </div>
  <input type="hidden" name="status" value="" id="hidden_status">
@else
<div class="col-12 mt-2">
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
