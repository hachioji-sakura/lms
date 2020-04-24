<div class="col-12 mt-2 couse_type_group">
  <div class="form-group">
    <label for="send_mail" class="w-100">
      {{__('labels.schedule_change_remind')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-grey" type="radio" name="send_mail" id="send_none" value="none" required="true"
      @if(!isset($default_send_teacher) || $default_send_teacher==false)
      checked
      @endif
      >
      <label class="form-check-label mr-3" for="send_none" checked>
        {{__('labels.no_remind')}}
      </label>
      <input class="form-check-input icheck flat-red" type="radio" name="send_mail" id="send_teacher" value="teacher" required="true"
      @if(isset($default_send_teacher) && $default_send_teacher==true)
      checked
      @endif
      >
      <label class="form-check-label mr-3" for="send_teacher">
        {{__('labels.send_to_teacher')}}
      </label>
      @if(isset($_edit) && $_edit==true && $item->status!='new')
      {{-- status=newではない場合に、生徒に連絡する可能性がある --}}
      <input class="form-check-input icheck flat-red" type="radio" name="send_mail" id="send_both" value="both" required="true" >
      <label class="form-check-label mr-3" for="send_both">
        {{__('labels.send_to_teacher_and_student')}}
      </label>
      @endif
    </div>
  </div>
</div>
