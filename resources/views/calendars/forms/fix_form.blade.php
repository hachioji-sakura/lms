<div class="col-12 mb-1">
  <div class="form-group">
    <label for="status">
      この授業予定に出席する
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="form-check">
          <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_fix" value="fix" required="true" onChange="status_radio_change()">
          <label class="form-check-label" for="status_fix">
              はい
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_cancel" value="cancel" required="true"  onChange="status_radio_change()">
          <label class="form-check-label" for="status_cancel">
              いいえ
          </label>
      </div>
    </div>
  </div>
</div>
<div class="col-12 collapse" id="cancel_reason">
  <div class="form-group">
    <label for="cancel_reason" class="w-100">
      授業予定に参加できない理由をお知らせください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <textarea type="text" name="cancel_reason" class="form-control" placeholder="例：予定日時の都合があわなくなり、X月X日 15時～に変更したい。" ></textarea>
  </div>
</div>
<script>
function status_radio_change(){
  console.log("status_radio_change");
  var is_cancel = $('input[type="radio"][name="status"][value="cancel"]').prop("checked");
  if(is_cancel){
    $("textarea[name='remark']").show();
    $("#cancel_reason").collapse("show");
    $("input.member_status").each(function() {
      $(this).val('cancel');
    });
  }
  else {
    $("textarea[name='remark']").hide();
    $("#cancel_reason").collapse("hide");
    $("input.member_status").each(function() {
      $(this).val('fix');
    });
  }
  console.log($("input[name=status]").val());
}
</script>
