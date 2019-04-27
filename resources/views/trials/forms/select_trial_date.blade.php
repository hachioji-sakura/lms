<div class="row">
  <div class="col-12 border-right">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-calendar-check mr-1"></i>
        体験授業日時
      </h5>
    </div>
  </div>
  <div class="col-md-4">
    @component('components.calendar', [
      'id' => 1,
      'defaultDate'=> date('Y-m-d',strtotime($item->trial_start_time1)),
      'minHour'=> date('H',strtotime($item->trial_start_time1)),
      'maxHour'=> date('H',strtotime($item->trial_end_time1)),
      'mode'=>'day',
      'user_id' => $candidate_teacher->user_id, 'teacher_id' => $candidate_teacher->id])
      @slot('event_select')
      @endslot
      @slot('event_click')
      eventClick: function(event, jsEvent, view) {
        $calendar.fullCalendar('unselect');
        base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id);
      },
      @endslot
      @slot('event_render')
      eventRender: function(event, element) {
        var title = '授業追加';
        if(event['student_name']){
          title = event['student_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
        }
        event_render(event, element, title);
      },
      @endslot
    @endcomponent
  </div>
  <div class="col-md-4">
    @component('components.calendar', [
      'id' => 2,
      'defaultDate'=> date('Y-m-d',strtotime($item->trial_start_time2)),
      'minHour'=> date('H',strtotime($item->trial_start_time2)),
      'maxHour'=> date('H',strtotime($item->trial_end_time2)),
      'mode'=>'day',
      'user_id' => $candidate_teacher->user_id, 'teacher_id' => $candidate_teacher->id])
      @slot('event_select')
      @endslot
      @slot('event_click')
      eventClick: function(event, jsEvent, view) {
        if(event.status == "trial") return false;
        $calendar.fullCalendar('unselect');
        base.showPage('dialog', "subDialog", "カレンダー詳細", "/calendars/"+event.id);
      },
      @endslot
      @slot('event_render')
      eventRender: function(event, element) {
        var title = '授業追加';
        if(event['student_name']){
          title = event['student_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
        }
        event_render(event, element, title);
      },
      @endslot
    @endcomponent
  </div>
  <div class="col-md-4">
    <span class="description-text">
      @if(count($candidate_teacher->trial) < 1)
      <h6 class="text-sm p-1 pl-2 mt-2 bg-danger" >
        <i class="fa fa-exclamation-triangle mr-1"></i>
        予定が空いていません
      </h6>
      @else
      <?php $is_first=false; ?>
        @foreach($candidate_teacher->trial as $i=>$_list)
          @if($_list['status']==='free')
          <div class="form-check ml-2" id="trial_select">
            <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial_{{$i}}"
             value="{{$_list['start_time']}}_{{$_list['end_time']}}"
             dulation="{{$_list['dulation']}}"
             start_time="{{$_list['start_time']}}"
             end_time="{{$_list['end_time']}}"
             lesson_place_floor="{{$_list['free_place_floor']}}"
             remark="{{$_list['remark']}}"
             onChange="teacher_schedule_change(this)"
             validate="teacher_schedule_validate('#trial_select')"
             @if($is_first==false) checked @endif
             >
            <label class="form-check-label" for="trial_{{$i}}">
              {{$_list['dulation']}}
              @if($_list['review']==="primary")
              ★★
              @elseif($_list['review']==="primary")
              ★
              @endif
            </label>
          </div>
      <?php $is_first=true; ?>
          @else
          {{-- 空いてない場合の表示はなくなる --}}
          <div class="form-check ml-2">
            <a  href="javascript:void(0);" page_title="授業予定" page_form="dialog" page_url="/calendars/{{$_list['conflict_calendar']->id}}">
            <label class="form-check-label" for="trial_{{$i}}">
              @if($_list['status']==='place_conflict')
              <i class="fa fa-times mr-1"></i>
              @elseif($_list['status']==='place_conflict')
              <i class="fa fa-calendar-times mr-1"></i>
              @else
              <i class="fa fa-times-circle mr-1"></i>
              @endif
              {{$_list['dulation']}}
            </label>
            </a>
          </div>
          @endif
        @endforeach
      @endif
    </span>
  </div>
</div>

<script >
$(function(){
  teacher_schedule_change();
});
function teacher_schedule_change(obj){
  console.log("teacher_schedule_change");
  var _teacher_schedule = $('input[name=teacher_schedule]:checked');
  if(_teacher_schedule.length<1) return false;
  var start = _teacher_schedule.attr('start_time');
  var end = _teacher_schedule.attr('end_time');
  $('input[name=start_time]').val(start);
  $('input[name=end_time]').val(end);
  var lesson_place_floor = _teacher_schedule.attr('lesson_place_floor');
  var select_lesson_place_floor = $("*[name='lesson_place_floor']").val();
  if(!util.isEmpty(lesson_place_floor) && util.isEmpty(select_lesson_place_floor)){
    $("*[name='lesson_place_floor']").val(lesson_place_floor);
  }
  var remark = _teacher_schedule.attr('remark');
  if(remark=="trial_date1"){
    $calendar = $("#calendar1");
  }
  else if(remark=="trial_date2"){
    $calendar = $("#calendar2");
  }
  $("#calendar2").fullCalendar("removeEvents", -1);
  $("#calendar1").fullCalendar("removeEvents", -1);
  $("#calendar2").fullCalendar("unselect");
  $("#calendar1").fullCalendar("unselect");
  $calendar.fullCalendar('addEventSource', [{
    id:-1,
    title: "体験授業",
    start: start,
    end : end,
    status : "trial",
  }]);

}
function teacher_schedule_validate(obj){
  var start_time = $('input[name=start_time]').val();
  var end_time = $('input[name=end_time]').val();
  console.log("teacher_schedule_validate"+start_time+":"+end_time);
  var is_school = $('input[type="checkbox"][name="lesson[]"][value="1"]').prop("checked");
  if(util.isEmpty(start_time) || util.isEmpty(end_time)){
    front.showValidateError(obj, '体験授業日時を指定してください');
    return false;
  }

  var _teacher_schedule = $('input[name=teacher_schedule]:checked');
  var lesson_place_floor = _teacher_schedule.attr('lesson_place_floor');
  var select_lesson_place_floor = $("*[name='lesson_place_floor']").val();
  if(!util.isEmpty(lesson_place_floor)){
    if(lesson_place_floor != select_lesson_place_floor){
      var lesson_place_floor_name = $('*[name=lesson_place_floor] option[value='+lesson_place_floor+']').text().trim();
      front.showValidateError($("*[name='lesson_place_floor']"), 'この予定は教室を、「'+lesson_place_floor_name+'」にしてください');
      return false;
    }
  }
  return true;
}
</script>
