<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="phone_no">
      {{__('labels.phone_no')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      <span class="text-sm">{!!nl2br(__('messages.warning_telephone_not_hyphen'))!!}</span>
    </label>
    <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222" required="true" inputtype="number"
      value="@if(isset($item) && isset($item['phone_no'])){{$item['phone_no']}}@endif"
      >
  </div>
</div>
