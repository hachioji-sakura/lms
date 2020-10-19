<?php
$d = $start_date;
?>
<div class="col-12">
  <h6 class="text-sm p-2 pl-3 bg-success" >
    受講を希望する日付にチェックを入れて、受講時間を指定してください。<br><br>
    受講日数分の日程がまだ不明な場合は、現在判明している分のみチェックを入れてください。<br>
  </h6>
</div>
@while(1)
  @if(strtotime($end_date) < strtotime($d)) @break @endif

<div class="col-12 bd-b bd-gray">
  <div class="row mb-2" id="{{strtotime($d)}}">
    <div class="col-12">
      <div class="form-check p-0">
        <input class="form-check-input icheck flat-green day_check" type="checkbox" name="hope_{{strtotime($d)}}" id="hope_{{strtotime($d)}}" value="true"
        onChange="hope_date_change('{{strtotime($d)}}')"
        />
        <label class="form-check-label" for="hope_{{strtotime($d)}}">
          {{date('n月d日',strtotime($d)).'('.config('week')[date('w', strtotime($d))].')'}}
        </label>
      </div>
      <div class="input-group  date-selected-open text-sm mt-1 collapse">
        <div class="form-check">
          <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_am" value="am"
          @if(isset($item) && isset($item['hope_{{strtotime($d)}}_timezone']) && $item['hope_{{strtotime($d)}}_timezone']==='am')
            checked
          @endif
          required="true"
          onChange="hope_timezone_change('{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_am">
          午前(11:00-16:00）
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input icheck flat-blue ml-1" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_pm" value="pm"
          @if(isset($item) && isset($item['hope_{{strtotime($d)}}_timezone']) && $item['hope_{{strtotime($d)}}_timezone']==='pm')
           checked ml-1
          @endif
          required="true"
          onChange="hope_timezone_change('{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_pm">
          午後(13:00-18:00）
          </label>
        </div>
      </div>
    </div>
    <div class="col-12 mt-1  date-selected-open text-sm collapse">
      <div class="input-group">
        <div class="form-check mt-2 mr-2">
          <input class="form-check-input icheck flat-red ml-1" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_order" value="order"
          @if(isset($item) && isset($item['hope_{{strtotime($d)}}_timezone']) && $item['hope_{{strtotime($d)}}_timezone']==='order')
           checked
          @endif
          required="true"
          onChange="hope_timezone_change('{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_order">
          指定
          </label>
        </div>
        <select name="hope_{{strtotime($d)}}_start_time" class="form-control mw-80px" required="true" disabled>
          <option value="">{{__('labels.selectable')}}</option>
          @for ($h = 8; $h < 23; $h++)
            <option value="{{$h}}"
            @if($_edit===true && 1==2)
            selected
            @endif

            >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
          @endfor
        </select>
        <span class="mt-2 ml-2">時 ～</span>
        <select name="hope_{{strtotime($d)}}_end_time" class="form-control mw-80px" required="true" greater="hope_{{strtotime($d)}}_start_time" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="hope_{{strtotime($d)}}_start_time" not_equal_error="{{__('messages.validate_timezone_error')}}" disabled>
          <option value="">{{__('labels.selectable')}}</option>
          @for ($h = 8; $h < 23; $h++)
            <option value="{{$h}}"
            @if($_edit===true && 1==2)
            selected
            @endif
            >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
            @endfor
        </select>
        <span class="mt-2 ml-2">時</span>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="day_count" value=0" />
  <?php
  $d = date('Y/m/d', strtotime('+1 day '.$d));
  ?>
@endwhile
<script>
$(function(){
  day_count_check_onload();
});
function hope_date_change(id){
  var date_checked = $("input[name='hope_"+id+"']").prop('checked');
  if(date_checked==true){
    $("#"+id+" .date-selected-open").collapse('show');
  }
  else {
    $("#"+id+" .date-selected-open").collapse('hide');
  }
  day_count_check_onload();
}
function day_count_check_onload(){
  c = 0;
  $("input.day_check").each(function(){
    if($(this).prop('checked')){
      c++;
    }
  });
  $('input[name="day_count"]').val(c);
  $('#day_count').html(c);
}
function hope_timezone_change(id){
  var timezone = $("input[name='hope_"+id+"_timezone']:checked").val();
  console.log('hope_timezone_change('+id+') / '+ timezone);
  if(timezone=="am" || timezone=="pm"){
    $("select[name='hope_"+id+"_start_time']").prop('disabled', true);
    $("select[name='hope_"+id+"_end_time']").prop('disabled', true);
    if(timezone=='am'){
      $("select[name='hope_"+id+"_start_time']").val(11);
      $("select[name='hope_"+id+"_end_time']").val(16);
    }
    else {
      $("select[name='hope_"+id+"_start_time']").val(13);
      $("select[name='hope_"+id+"_end_time']").val(18);
    }
  }
  else {
    $("select[name='hope_"+id+"_start_time']").prop('disabled', false);
    $("select[name='hope_"+id+"_end_time']").prop('disabled', false);
  }
}

</script>
