<div class="col-12 mb-2">
  <label for="start_date" class="w-100">
    設定有効日
    <span class="right badge badge-secondary ml-1">任意</span>
  </label>
  <div class="input-group">
    <input type="text" id="enable_start_date" name="start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01" minvalue="{{date('Y/m/d')}}"
    @if(isset($item) && isset($item['enable_start_date']))
    value = "{{date('Y/m/d', strtotime($item['enable_start_date']))}}"
    @endif
    >
    <span class="float-left mx-2 mt-2">～</span>
    <input type="text" id="enable_end_date" name="end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01" minvalue="{{date('Y/m/d')}}"
    @if(isset($item) && isset($item['enable_end_date']))
    value = "{{date('Y/m/d', strtotime($item['enable_end_date']))}}"
    @endif
    >
  </div>
</div>
