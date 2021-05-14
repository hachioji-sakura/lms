<div class="col-6 mb-2">
  <label for="enable_start_date" class="w-100">
    {{__('labels.schedule_start_date')}}
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="enable_start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01" required="true"
    @if($_edit==false)
    minvalue="{{date("Y/m/01", strtotime('+1 month'))}}"
    @elseif($_edit==true)
    minvalue="{{$item->enable_start_date}}"
    @endif
    @if(isset($item) && isset($item['enable_start_date']) && $item['enable_start_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_start_date']))}}"
    @elseif(isset($item) && isset($item->schedule_start_hope_date))
    value = "{{date('Y/m/d', strtotime($item->schedule_start_hope_date))}}"
    @endif
    >
  </div>
</div>
<div class="col-6 mb-2">
  <label for="enable_end_date" class="w-100">
    {{__('labels.schedule_end_date')}}
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="enable_end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && isset($item['enable_end_date']) && $item['enable_end_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_end_date']))}}"
    @endif
    >
  </div>
</div>
