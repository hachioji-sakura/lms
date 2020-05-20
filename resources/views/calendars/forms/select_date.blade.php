<div class="col-12">
  <div class="form-group">
    <label for="start_date" class="w-100">
      {{__('labels.date')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
      @if(isset($_edit) && $_edit==true && isset($item) && isset($item['start_time']))
        value="{{date('Y/m/d', strtotime($item['start_time']))}}"
      @elseif(isset($item) && isset($item['start_date']))
        value="{{$item['start_date']}}"
      @endif
      @if(!(isset($_edit) && $_edit==true))
      @endif
      >
    </div>
  </div>
</div>
