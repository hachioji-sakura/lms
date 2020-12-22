<div class="col-12 mt-1">
  <label for="season_lesson_course" class="w-100">
    @if(!isset($title) || empty($title))
    ご希望の時間帯について
    @else
    {{$title}}
    @endif
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  @if(isset($is_student) && $is_student==true)
  <label class="mx-2" for="hope_timezone_am">
    <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_timezone" id="hope_timezone_am" value="am"
      @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "am"))
      checked
      @endif
      onChange="hope_timezone_all_set()"
      required="true">
      午前(11:00-16:00）
  </label>
  <label class="mx-2" for="hope_timezone_pm">
    <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_timezone" id="hope_timezone_pm" value="pm"
      @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "pm"))
      checked
      @endif
      onChange="hope_timezone_all_set()"
      required="true">
      午後(13:00-18:00）
  </label>
  @endif
</div>
<div class="col-12 mt-1 mb-2">
  <div class="input-group">
    @if(isset($is_student) && $is_student==true)
    <label class="mx-2" for="hope_timezone_order">
      <input class="form-check-input icheck flat-red ml-1" type="radio" name="hope_timezone" id="hope_timezone_order" value="order"
        @if(isset($item) && isset($item->id) && $item->has_tag("hope_timezone", "order"))
        checked
        @endif
        onChange="hope_timezone_all_set()"
        required="true">
        指定
    </label>
    @endif
    <select name="hope_start_time" class="form-control mw-80px" required="true"
    @if(isset($is_student) && $is_student==true)
    disabled
    @endif
    >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($h = 11; $h < 19; $h++)
        <option value="{{$h}}"
        @if($_edit===true )
        selected
        @endif
        >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
      @endfor
    </select>
    <span class="mt-2 ml-2">時 ～</span>
    <select name="hope_end_time" class="form-control mw-80px" required="true" greater="hope_start_time" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="hope_start_time" not_equal_error="{{__('messages.validate_timezone_error')}}"
    @if(isset($is_student) && $is_student==true)
    disabled
    @endif
    >
      <option value="">{{__('labels.selectable')}}</option>
      @for ($h = 11; $h < 19; $h++)
        <option value="{{$h}}"
        @if($_edit===true )
        selected
        @endif
        >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
      @endfor
    </select>
    <span class="mt-2 ml-2">時</span>
  </div>
</div>
<div class="col-12">
  <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
    次ページの勤務可能日と時間帯を入力する際、初期値となります。
  </h6>
</div>
<script>
function hope_timezone_all_set(){
  console.log('hope_timezone_all_set');
  var timezone = $("input[name='hope_timezone']:checked").val();
  if(!timezone) return;
  if(timezone=="am" || timezone=="pm"){
    $("select[name='hope_start_time']").prop('disabled', true);
    $("select[name='hope_end_time']").prop('disabled', true);
    if(timezone=='am'){
      $("select[name='hope_start_time']").val(11);
      $("select[name='hope_end_time']").val(16);
    }
    else {
      $("select[name='hope_start_time']").val(13);
      $("select[name='hope_end_time']").val(18);
    }
  }
  else {
    $("select[name='hope_start_time']").prop('disabled', false);
    $("select[name='hope_end_time']").prop('disabled', false);
  }
}
</script>
