<div class="col-6 mt-2">
  <div class="form-group">
    <label for="send_mail" class="w-100">
      {{__('labels.')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-green" type="radio" name="action" id="action_status_update" value="status_update" required="true" onChange="select_action_change();"
      checked
      >
      <label class="form-check-label mr-3" for="action_status_update">
        {{__('labels.')}}
      </label>
      <input class="form-check-input icheck flat-red" type="checkbox" name="action" value="remind" required="true" >
      <label class="form-check-label mr-3" for="action_remind">
        {{__('labels.online')}}
      </label>
    </div>
  </div>
</div>
