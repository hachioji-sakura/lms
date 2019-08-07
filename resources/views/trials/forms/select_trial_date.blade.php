<div class="row">
  <div class="col-12 border-right">
    <div class="description-block">
      <h5 class="description-header">
        <i class="fa fa-calendar-check mr-1"></i>
        体験授業日時
      </h5>
    </div>
  </div>
  <div class="col-12">
    <div class="form-group">
      <?php
      $d = [1=>date('Y-m-d',strtotime($item->trial_start_time1)),
            2=>date('Y-m-d',strtotime($item->trial_start_time2)),
            3=>date('Y-m-d',strtotime($item->trial_start_time3))];
      $is_first = true;
        ?>
      @for($i=1;$i<4;$i++)
      <label class="mx-2">
        @if(isset($candidate_teacher->trial["trial_date".$i]) && count($candidate_teacher->trial["trial_date".$i])>0)
          {{-- 空き予定が存在する --}}
          <input type="radio" name="trial_date_hope" value="{{$d[$i]}}" class="icheck flat-green" required="true"
          @if($is_first==true)
          checked
          <?php $is_first=false; ?>
          @endif
          attr="{{$i}}"
          onChange="trial_date_hope_change()"
          >
        @else
          <i class="fa fa-times mr-1"></i>
        @endif
        第{{$i}}希望({{$d[$i]}})
      </label>
      @endfor
    </div>
  </div>
  <script>
  function trial_date_hope_change(no){
    var check_date = $("input[name='trial_date_hope']:checked");
    var d = check_date.val();
    var no = check_date.attr('attr');
    if(!d) return ;
    $(".teacher_schedule").hide();
    $(".teacher_schedule[remark='trial_date"+no+"']").show();
    $("#calendar1").fullCalendar('gotoDate', d);
    console.log("trial_date_hope_change");
  }
  </script>
  <div class="col-md-6">
    @component('components.calendar', [
      'id' => 1,
      'defaultDate'=> date('Y-m-d',strtotime($item->trial_start_time1)),
      'mode'=>'day',
      'user_id' => $candidate_teacher->user_id, 'teacher_id' => $candidate_teacher->id])
      @slot('event_select')
      @endslot
      @slot('event_click')
      eventClick: function(event, jsEvent, view) {
        $calendar.fullCalendar('unselect');
        base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/calendars/"+event.id);
      },
      @endslot
      @slot('event_render')
      eventRender: function(event, element) {
        var title = '{{__('labels.schedule_add')}}';
        if(event['student_name']){
          title = event['student_name']+'('+event['subject']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
        }
        event_render(event, element, title, true);
      },
      @endslot
    @endcomponent
  </div>
  <div class="col-md-6" id="trial_select">
    <span class="description-text">
      @if(count($candidate_teacher->trial) < 1)
      <h6 class="text-sm p-1 pl-2 mt-2 bg-danger" >
        <i class="fa fa-exclamation-triangle mr-1"></i>
        予定が空いていません
      </h6>
      @else
      <?php $is_first=false; $c=0; ?>
        @foreach($candidate_teacher->trial as $remark=>$_lists)
          @foreach($_lists as $i => $_list)
            @if($_list['status']==='free')
            <div class="form-check ml-2 teacher_schedule" remark="{{$_list['remark']}}">
              <input class="form-check-input icheck flat-green" type="radio" name="teacher_schedule" id="trial_{{$c}}"
               value="{{$_list['start_time']}}_{{$_list['end_time']}}"
               duration="{{$_list['duration']}}"
               start_time="{{$_list['start_time']}}"
               end_time="{{$_list['end_time']}}"
               lesson_place_floor="{{$_list['free_place_floor']}}"
               remark="{{$remark}}"
               onChange="teacher_schedule_change(this)"
               validate="teacher_schedule_validate('#trial_select')"
               @if($is_first==false) checked @endif
               >
              <label class="form-check-label" for="trial_{{$c}}" title="{{$_list['review']}}">
                {{$_list['duration']}}
              </label>
            </div>
            <?php $is_first=true; $c++; ?>
            @else
            {{-- 空いてない場合の表示はなくなる --}}
            <div class="form-check ml-2">
              @if(isset($_list['conflict_calendar']) && isset($_list['conflict_calendar']->id))
              <a  href="javascript:void(0);" page_title="{{__('labels.schedule_details')}}" page_form="dialog" page_url="/calendars/{{$_list['conflict_calendar']->id}}">
              @endif
              <label class="form-check-label" for="trial_{{$i}}">
                @if($_list['status']==='place_conflict')
                <i class="fa fa-times mr-1"></i>
                @elseif($_list['status']==='time_conflict')
                <i class="fa fa-calendar-times mr-1"></i>
                @else
                <i class="fa fa-times-circle mr-1"></i>
                @endif
                {{$_list['duration']}}
              </label>
              @if(isset($_list['conflict_calendar']) && isset($_list['conflict_calendar']->id))
              </a>
              @endif
            </div>
            @endif
          @endforeach
        @endforeach
      @endif
    </span>
  </div>
</div>

<script >
$(function(){
  trial_date_hope_change();
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
  if(!util.isEmpty(lesson_place_floor)){
    $("*[name='lesson_place_floor']").val(lesson_place_floor);
  }
  $calendar = $("#calendar1");
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
