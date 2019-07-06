@if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00') && $item['trial_id'] == 0)
<div class="col-12 mt-2 mb-1">
  <div class="form-group">
    <input class="form-check-input icheck flat-green" type="checkbox" id="agreement" name="agreement" value="1" required="true" >
    <label class="form-check-label" for="agreement">
      振替対象外となることを確認しました。
    </label>
  </div>
</div>
@endif
{{--
  休み理由は不要 5/17
<div class="col-12" id="cancel_reason">
  <div class="form-group">
    <label for="rest_reason" class="w-100">
      お休みの理由をお知らせください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <textarea type="text" name="rest_reason" class="form-control" placeholder="例：予定日時の都合があわなくなり、X月X日 15時～に変更したい。"></textarea>
  </div>
</div>
--}}
