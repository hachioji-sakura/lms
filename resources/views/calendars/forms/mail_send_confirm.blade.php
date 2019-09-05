<div class="col-12 mt-2 couse_type_group">
  <div class="form-group">
    <label for="send_mail" class="w-100">
      予定変更を連絡する
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" >
      <input class="form-check-input icheck flat-grey" type="radio" name="send_mail" id="send_none" value="none" required="true" checked>
      <label class="form-check-label mr-3" for="send_none" checked>
        通知しない
      </label>
      <input class="form-check-input icheck flat-red" type="radio" name="send_mail" id="send_teacher" value="teacher" required="true" >
      <label class="form-check-label mr-3" for="send_teacher">
        講師に通知
      </label>
      @if($item->status!='new')
      {{-- status=newではない場合に、生徒に連絡する可能性がある --}}
      <input class="form-check-input icheck flat-red" type="radio" name="send_mail" id="send_both" value="both" required="true" >
      <label class="form-check-label mr-3" for="send_both">
        生徒・講師に通知
      </label>
      @endif
    </div>
  </div>
</div>
