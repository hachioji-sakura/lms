<div class="col-12">
  <label for="charge_subject" class="w-100">
    曜日
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <?php $is_first=true; ?>
  @foreach($attributes['lesson_week'] as $week_day => $week_name)
    @if(count($teacher->match_schedule['result'][$week_day]) > 0)
    <label class="mx-2">
      <input type="radio" name="select_lesson_week" value="{{$week_day}}" class="icheck flat-green" required="true"
      onChange="select_lesson_week_change()"
      @if($is_first==true)
        checked
        <?php $is_first=false; ?>
      @endif
      >{{$week_name}}曜日
    </label>
    @endif
  @endforeach
</div>
<div class="col-8 mb-2">
  <div class="description-block">
    <h5 class="description-header">
        <i class="fa fa-calendar mr-1"></i>
        定期スケジュール
    </h5>
    <span class="description-text">
      <table class="table border-bottom">
      <tr class="bg-secondary header calendar_setting_header">
        <th class="p-1 text-center border-right  action_form action_add">
          選択
        </th>
        <th class="p-1 text-center border-right ">
          曜日/時間
        </th>
        <th class="p-1 text-center border-right ">
          生徒
        </th>
        <th class="p-1 text-center border-right ">
          内容
        </th>
      </tr>
      @foreach($attributes['lesson_week'] as $week_day => $week_name)
        {{-- 必要な曜日の予定のみ表示 --}}
        @if(count($teacher->match_schedule['result'][$week_day]) > 0)
          @if(isset($teacher->user->calendar_setting()['week'][$week_day]))
            @foreach($teacher->user->calendar_setting()['week'][$week_day] as $setting)
            @if($setting->is_enable()==false) @continue @endif
            <tr id="{{$week_day}}_{{$setting["from_time_slot"]}}_{{$setting["to_time_slot"]}}" class="calendar_setting_row {{$week_day}}">
              <td class="action_form action_add">
                <input class="form-check-input icheck flat-green" type="radio" name="calendar_setting_id" value="{{$setting->id}}" >
              </td>
              <td>
                {{$week_name}}
                {{$setting->timezone()}}
              </td>
              <td>
                @foreach($setting->details()['students'] as $member)
                <a class="text-xs mx-2" alt="student_name" href="/students/{{$member->user->student->id}}" target="_blank">
                    {{$member->user->student->name()}}
                </a>
                @endforeach
              </td>
              <td>
                <span class="text-xs mx-2">
                  <small class="badge badge-success mt-1 mr-1">
                    {{$setting["place_floor_name"]}}
                  </small>
                </span>
                <span class="text-xs mx-2">
                  <small class="badge badge-info mt-1 mr-1">
                    {{$setting->course()}}
                  </small>
                </span>
                @foreach($setting->subject() as $index => $name)
                <span class="text-xs mx-2">
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$name}}
                  </small>
                </span>
                @endforeach
              </td>
            </tr>
            @endforeach
          @endif
        @endif
      @endforeach
      </table>
    </span>
  </div>
</div>
<div class="col-4 mb-2 action_form action_new">
  <div class="description-block">
    <h5 class="description-header">
        <i class="fa fa-check-circle mr-1"></i>
        曜日・時間帯を選択
    </h5>
    <span class="description-text">
      <?php
       $is_first=false;
      ?>
      @foreach($attributes['lesson_week'] as $week_day => $week_name)
        {{-- 必要な曜日の予定のみ表示 --}}
        @if(count($teacher->match_schedule['result'][$week_day]) > 0)
            @foreach($teacher->match_schedule['result'][$week_day] as $time_slot)
              @if($time_slot["show"]==true)
              @else
                @continue;
              @endif
              <span class="teacher_schedule {{$week_day}}">
              @if($time_slot["status"]=="free")
                <input class="form-check-input icheck flat-green lesson_week_datetime" type="radio" name="teacher_schedule" id="lesson_week_{{$week_day}}_{{$time_slot["from"]}}_{{$time_slot["to"]}}"
                 value="{{$week_day}}_{{$time_slot["from"]}}_{{$time_slot["to"]}}"
                 week_day="{{$week_day}}"
                 start_time="{{$time_slot["from"]}}"
                 end_time="{{$time_slot["to"]}}"
                 lesson_place_floor="{{$time_slot["place"]}}"
                 onChange="lesson_week_datetime_change()"
                 @if($is_first==false)  @endif
                 >
                 <label class="form-check-label" for="lesson_week_{{$week_day}}_{{$time_slot["from"]}}_{{$time_slot["to"]}}" title="{{$time_slot["review"]}}_{{$time_slot["show"]}}">
                   {{$week_name}}
                   {{$time_slot["from"]}}～{{$time_slot["to"]}}
                 </label>
                 <?php $is_first=true; ?>
              @else
                <i class="fa fa-times mr-1"></i>
                {{$week_name}}
                {{$time_slot["from"]}}～{{$time_slot["to"]}}
              @endif
              <br>
              </span>
              </td>
            </tr>
            @endforeach
        @endif
      @endforeach
      </table>
    </span>
  </div>
</div>
<input type="hidden" name="from_time_slot" value="">
<input type="hidden" name="to_time_slot" value="">
<input type="hidden" name="lesson_week" value="">
<script>
$(function(){
  select_lesson_week_change();
  lesson_week_datetime_change();
  $("select[name='charge_subject[]']").on('change', lesson_week_datetime_change);
  $("select[name='lesson_place_floor']").on('change', lesson_week_datetime_change);
});
function lesson_week_datetime_change(){
  var lesson_week_datetime = $("input.lesson_week_datetime:checked");
  if(!lesson_week_datetime) return;
  var select_id = lesson_week_datetime.attr("id");
  var week_day = lesson_week_datetime.attr("week_day");
  var start_time = lesson_week_datetime.attr("start_time");
  var end_time = lesson_week_datetime.attr("end_time");
  var lesson_place_floor = lesson_week_datetime.attr('lesson_place_floor');
  if(!select_id) return;
  $("input[name='lesson_week']").val(week_day);
  $("input[name='from_time_slot']").val(start_time.substr(0,2)+':'+start_time.substr(-2,2)+':00');
  $("input[name='to_time_slot']").val(end_time.substr(0,2)+':'+end_time.substr(-2,2)+':00');
  start_time = (start_time+'00')|0;
  end_time = (end_time+'00')|0;
  var select_lesson_place_floor = $("select[name='lesson_place_floor']").val();
  if(!util.isEmpty(lesson_place_floor)){
    $("select[name='place_floor_id']").val(lesson_place_floor);
  }
  var lesson_place_floor_name = $('select[name="place_floor_id"] option:selected').text().trim();
  var _label = $("label[for='"+select_id+"']").html();
  var _detail = "";
  var _template =[
    '<span class="text-xs mx-2">',
      '<small class="badge badge-#style# mt-1 mr-1">',
      '#item#',
      '</small>',
    '</span>',
  ].join('');
  _detail+=_template.replace('#item#', lesson_place_floor_name).replace('#style#', 'success');
  var _names = ["grade", "student2_grade", "student3_grade"];
  $("select[name='charge_subject[]'] option:selected").each(function(index, value){
    var option_name = $(this).html();
    _detail+=_template.replace('#item#', option_name).replace('#style#', 'primary');
  });
  $("select[name='english_talk_lesson[]'] option:selected").each(function(index, value){
    var option_name = $(this).html();
    _detail+=_template.replace('#item#', option_name).replace('#style#', 'primary');
  });
  $("select[name='kids_lesson[]'] option:selected").each(function(index, value){
    var option_name = $(this).html();
    _detail+=_template.replace('#item#', option_name).replace('#style#', 'primary');
  });
  $("select[name='piano_lesson[]'] option:selected").each(function(index, value){
    var option_name = $(this).html();
    _detail+=_template.replace('#item#', option_name).replace('#style#', 'primary');
  });

  var _html = [
    '<tr class="bg-warning" id="new_row">',
    '<td>'+_label+'</td>',
    '<td>体験</td>',
    '<td>'+_detail+'</td>',
    '</tr>',
  ].join('');

  var _element = null;
  var _is_before = false;
  $(".calendar_setting_row").each(function(index){
    var id = $(this).attr("id");
    var _id = (id+"_").split("_");
    var _week_day = _id[0];
    var _start_time = _id[1].replace_all(':','')|0;
    var _end_time = _id[2].replace_all(':','')|0;
    if(_element==null && _week_day==week_day){
      _element = this;
      _is_before = true;
    }
    if(_week_day==week_day && start_time > _start_time){
      _element = this;
      _is_before = false;
    }
  });
  $('#new_row').remove();
  if(_element==null){
    $(".calendar_setting_header").after(_html);
  }
  else {
    if(_is_before)  $(_element).before(_html);
    else $(_element).after(_html);
  }
}
function select_lesson_week_change(){
  var w = $('input[name="select_lesson_week"]:checked').val();
  if(!w) return ;
  console.log("select_lesson_week_change:"+w);
  $(".teacher_schedule").hide();
  $(".teacher_schedule."+w).show();
  $(".calendar_setting_row").hide();
  $(".calendar_setting_row."+w).show();
}
</script>
