<?php $m = 0; ?>
@foreach($event_dates as $d)
  @if($m != date('m', strtotime($d)))
  <div class="col-12 mt-2 bd-b bd-gray">
    <div class="form-group">
      <label class="w-100 text-lg">
          {{date('Y年n月', strtotime($d))}}
      </label>
    </div>
  </div>
  @endif
  <?php
    $m = date('n', strtotime($d));
    if($_edit==true)  $hope_date = $item->get_hope_date($d);
    $w = date('w', strtotime($d));
    $week = ["sun", "mon", "tue", "wed", "thi", "fri", "sat"];
    $lesson_week = $week[$w];
    $is_date_checked = false;
    if(isset($item)){
      $from_hour = 23;
      $to_hour = 0;
      $tags = $item->get_tags('season_lesson_'.$lesson_week.'_time');
      if($tags != null){
        foreach($tags as $tag){
          $hours = explode('_', $tag->tag_value);
          if(intval($hours[0]) < $from_hour) $from_hour = intval($hours[0]);
          if(intval($hours[1]) > $to_hour) $to_hour = intval($hours[1]);
        }
        if($from_hour < $to_hour ){
          $is_date_checked = true;
        }
      }
    }
  ?>

<div class="col-12 bd-b bd-gray ">
  <div class="row mb-2" id="hope_{{strtotime($d)}}">
    <div class="col-12">
      <div class="form-check p-0">
        <input type="hidden" name="hope_datetime[]" value="" accessKey="hope_{{strtotime($d)}}" >
        <input class="form-check-input icheck flat-green day_check" type="checkbox" name="hope_{{strtotime($d)}}_date" id="hope_{{strtotime($d)}}_date" date="{{date('Y-m-d', strtotime($d))}}" value="true"
        @if($is_date_checked == true)
          checked
        @endif
        @if(isset($hope_date)) checked @endif
        onChange="hope_date_change('hope_{{strtotime($d)}}')"
        />
        <label class="form-check-label" for="hope_{{strtotime($d)}}_date">
          {{date('n月d日',strtotime($d)).'('.config('week')[date('w', strtotime($d))].')'}}
        </label>
      </div>
      @if(isset($is_student) && $is_student==true)
      <div class="input-group  date-selected-open text-sm mt-1 collapse">
        <div class="form-check">
          <input class="form-check-input icheck flat-blue ml-1 hope_date_timezone" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_am" value="am"
          required="true"
          onChange="hope_timezone_change('hope_{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_am">
          午前(11:00-16:00）
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input icheck flat-blue ml-1 hope_date_timezone" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_pm" value="pm"
          required="true"
          onChange="hope_timezone_change('hope_{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_pm">
          午後(13:00-18:00）
          </label>
        </div>
      </div>
      @endif
    </div>
    <div class="col-12 mt-1 date-selected-open text-sm collapse">
      <div class="input-group">
        @if(isset($is_student) && $is_student==true)
        <div class="form-check mt-2 mr-2">
          <input class="form-check-input icheck flat-red ml-1 hope_date_timezone" type="radio" name="hope_{{strtotime($d)}}_timezone" id="hope_{{strtotime($d)}}_order" value="order"
          required="true"
          onChange="hope_timezone_change('hope_{{strtotime($d)}}')"
          >
          <label class="form-check-label" for="hope_{{strtotime($d)}}_order">
          指定
          </label>
        </div>
        @endif
        <select name="hope_{{strtotime($d)}}_start_time" class="form-control mw-80px hope_date_start_time" required="true"
        @if(isset($is_student) && $is_student==true)
        disabled
        @endif
        >
          <option value="">{{__('labels.selectable')}}</option>
          @for ($h = 11; $h < 19; $h++)
            <option value="{{$h}}"
            @if($_edit===true && isset($hope_date) && $hope_date->from_hour==$h)
            selected
            @elseif($_edit===false && $from_hour==$h)
            selected
            @endif

            >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}</option>
          @endfor
        </select>
        <span class="mt-2 ml-2">時 ～</span>
        <select name="hope_{{strtotime($d)}}_end_time" class="form-control mw-80px hope_date_end_time" required="true" greater="hope_{{strtotime($d)}}_start_time" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="hope_{{strtotime($d)}}_start_time" not_equal_error="{{__('messages.validate_timezone_error')}}"
        @if(isset($is_student) && $is_student==true)
        disabled
        @endif
        >
          <option value="">{{__('labels.selectable')}}</option>
          @for ($h = 11; $h < 19; $h++)
            <option value="{{$h}}"
            @if($_edit===true && isset($hope_date) && $hope_date->to_hour==$h)
            selected
            @elseif($_edit===false && $to_hour==$h)
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
@endforeach
<script>
$(function(){
  $('.carousel-item .btn-next').on('click', function(e){
    //carouselの次へで、集計処理する必要がある
    day_count_check_onload();
  });
  day_count_check_onload();
});
function hope_date_change(id){
  var date_checked = $("#"+id+"_date").prop('checked');
  if(date_checked==true){
    var timezone = $("input[name='hope_timezone']:checked").val();
    console.log('hope_date_change:'+timezone);
    if(!timezone) timezone = $("input[name='hope_timezone'][type='hidden']").val();
    if(timezone){
      $('input[name="'+id+'_timezone"][value="'+timezone+'"]').iCheck('check');
      if(util.isEmpty($('select[name="'+id+'_start_time"]').val())) $('select[name="'+id+'_start_time"]').val($("select[name='hope_start_time']").val());
      if(util.isEmpty($('select[name="'+id+'_end_time"]').val())) $('select[name="'+id+'_end_time"]').val($("select[name='hope_end_time']").val());
    }
    $("#"+id+" .date-selected-open").collapse('show');
  }
  else {
    $("#"+id+" .date-selected-open").collapse('hide');
  }
}
function day_count_check_onload(){
  var c = 0;
  var weight = 1;
  var course_minutes = $('input[name="season_lesson_course"]:checked').val()|0;
  if(course_minutes==120) weight = 2;
  $('#hope_datetime_list').empty();
  $("input.day_check").each(function(){
    if($(this).prop('checked')){
      c++;
      var id = $(this).attr('id');
      id = id.replace_all('_date', '');
      hope_date_change(id);
      var d = $(this).attr('date');
      var s = $('select[name="'+id+'_start_time"]').val();
      var e = $('select[name="'+id+'_end_time"]').val();
      var t = $('input[name="'+id+'_timezone"]:checked').val();
      var t_name = $('label[for="'+id+'_'+t+'"]').text();
      if(!(t=="pm" || t=="am")) t_name = ' '+s+'時～'+e+'時';
      console.log(t_name);
      date = d.replace_all('-', '');
      date = util.dateformat(date, '%m月%d(%w)');
      var v = d+" "+s+"-"+e;
      var _html = ['<tr>',
        '<td class="bg-gray">'+date+'</td>',
        '<td>'+t_name+'</td>',
       '</tr>'
      ].join('');
      $('#hope_datetime_list').append(_html);
      console.log(v);
      $('input[name="hope_datetime[]"][accessKey="'+id+'"]').val(v);
    }
  });
  c = c*weight;
  $('input[name="day_count"]').val(c);
  $('#day_count').html(c);
}
function hope_timezone_change(id){
  var timezone = $("input[name='"+id+"_timezone']:checked").val();
  if(!timezone) return;
  console.log('hope_timezone_change('+id+') / '+ timezone);
  if(timezone=="am" || timezone=="pm"){
    $("select[name='"+id+"_start_time']").prop('disabled', true);
    $("select[name='"+id+"_end_time']").prop('disabled', true);
    if(timezone=='am'){
      $("select[name='"+id+"_start_time']").val(11);
      $("select[name='"+id+"_end_time']").val(16);
    }
    else {
      $("select[name='"+id+"_start_time']").val(13);
      $("select[name='"+id+"_end_time']").val(18);
    }
  }
  else {
    $("select[name='"+id+"_start_time']").prop('disabled', false);
    $("select[name='"+id+"_end_time']").prop('disabled', false);
  }
}

</script>
