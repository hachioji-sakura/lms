<div class="col-12 mb-2">
  <label for="start_date" class="w-100">
    {{__('labels.setting_enable_day')}}
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="enable_start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && isset($item['enable_start_date']) && $item['enable_start_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_start_date']))}}"
    @endif
    >
    <span class="float-left mx-2 mt-2">～</span>
    <input type="text" name="enable_end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && isset($item['enable_end_date']) && $item['enable_end_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_end_date']))}}"
    @endif
    >
  </div>
</div>
