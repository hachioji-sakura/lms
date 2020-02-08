<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="phone_no">
      {{__('labels.phone_no')}}
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      <span class="text-sm">{!!nl2br(__('messages.warning_telephone_not_hyphen'))!!}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <h5>{{$item['phone_no']}}</h5>
    <input type="hidden" name="phone_no" value="{{$item['phone_no']}}" />
    @else
    <input type="text" id="phone_no" name="phone_no" class="form-control" placeholder="例：09011112222" required="true" inputtype="number"
      value="@if(isset($item) && isset($item['phone_no'])){{$item['phone_no']}}@endif"
      >
    @endif
  </div>
</div>
