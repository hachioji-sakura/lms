@if($item->is_prev_rest_contact()==false && $item['trial_id'] == 0)
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
@if(isset($student_id) && $student_id>0 && $item->own_member->user->student->is_arrowre()==true)
  {{-- student_idが指定されている場合、かつ、アローレの場合 --}}
<div class="col-12">
  <div class="form-group">
    <label class="w-100">
      お休みの理由をお知らせください
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <label class="mx-2">
      <input type="radio" value="自己都合" name="rest_reason" class="icheck flat-green" required="true"
      >自己都合
    </label>
    <label class="mx-2">
      <input type="radio" value="アローレ都合" name="rest_reason" class="icheck flat-green" required="true"
      >アローレ都合
    </label>
  </div>
</div>
@endif
