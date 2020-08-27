@if(isset($item['exchanged_calendar_id']) && $item['exchanged_calendar_id'] > 0)
<input type="hidden" name="exchanged_calendar_id" value="{{$item['exchanged_calendar_id']}}">
<input type="hidden" name="exchanged_calendar_datetime" value="">
<script>
$(function(){
  get_exchange_calendar();
});
function get_exchange_calendar(){
  console.log('get_exchange_calendar');
  var schedule_type = $("input[name='schedule_type']:checked").val();
  if(!schedule_type) {
    schedule_type = $("input[name='schedule_type'][type='hidden']").val();
  }
  if(!schedule_type) {
    return false;
  }
  if(schedule_type!="class"){
    $('input[name=exchanged_calendar_datetime]').val("");
    $('input[name=exchanged_calendar_id]').val("");
    return false;
  }
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  var student_id = $('select[name="student_id[]"]').val()|0;
  var lesson = ($('input[name=lesson]:checked').val())|0;
  var course_minutes = ($('input[name=course_minutes]:checked').val())|0;

  var exchanged_calendar_id = ($('input[name=exchanged_calendar_id]').val())|0;
  if(lesson==0){
    lesson = ($('input[name=lesson]').val())|0;
  }
  var url = '';
  if(exchanged_calendar_id>0){
    url = '/api_calendars?id='+exchanged_calendar_id+'&teacher_id='+teacher_id;
  }
  else if(student_id > 0){
    url = '/api_calendars?teacher_id='+teacher_id+'&student_id='+student_id+'&exchange_target=1&lesson='+lesson;
  }
  console.log("get_exchange_calendar:"+url);
  $('.add_type').hide();
  $('.add_type.add_type_new').show();
  //振替対象の予定を取得
  if(url == '') return;

  service.getAjax(false, url, null,
    function(result, st, xhr) {
      console.log(result["data"]);
      if(result['status']===200){
        if(result["data"].length>0){
          var val = result["data"][0];
          if(val["exchange_remaining_time"] >= course_minutes){
            $('input[name=exchanged_calendar_datetime]').val(val['datetime']);
            $('input[name=exchanged_calendar_id]').val(val['id']);
            $('.add_type.add_type_new').hide();
            $('.add_type.add_type_exchange').show();
          }
        }
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー(api_calendars)");
    }
  );
}
</script>
@endif
