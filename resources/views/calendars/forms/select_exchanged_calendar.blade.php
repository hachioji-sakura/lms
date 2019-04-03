<div class="col-12 col-lg-6 col-md-6 mb-1 student_selected collapse">
  <div class="form-group">
    <label for="status">
      振替授業
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      <div class="form-check">
          <input class="form-check-input icheck flat-green" type="radio" name="add_type" id="add_type_add" value="add" required="true" onChange="add_type_change()">
          <label class="form-check-label" for="add_type_add">
              追加
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="add_type" id="add_type_exchange" value="exchange" required="true" onChange="add_type_change()">
          <label class="form-check-label" for="add_type_exchange">
              振替
          </label>
      </div>
    </div>
  </div>
</div>
<div class="col-12 col-lg-6 col-md-6 collapse" id="exchanged_calendar">
  <div class="form-group">
    <label for="exchanged_calendar_id" class="w-100">
      振替対象の授業
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="exchanged_calendar_id" class="form-control" placeholder="振替対象の授業">
    </select>
  </div>
</div>
<script>
function add_type_change(obj){
  var is_exchange = $('input[type="radio"][name="add_type"][value="exchange"]').prop("checked");
  if(is_exchange){
    $("select[name='exchanged_calendar_id']").show();
    $("#exchanged_calendar").collapse("show");
  }
  else {
    $("select[name='exchanged_calendar_id']").hide();
    $("#exchanged_calendar").collapse("hide");
  }
  get_exchange_calendar();
}
var lectures = {};
function get_exchange_calendar(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  var student_id = $('select[name=student_id]').val()|0;
  service.getAjax(false, '/api_calendars?teacher_id='+teacher_id+'&student_id='+student_id+'&exchange_target=1', null,
    function(result, st, xhr) {
      if(result['status']===200){
        $('select[name="exchanged_calendar_id"]').html('');
        $.each(result['data'], function(id, val){
          var _option = '<option value="'+val['id']+'">'+val['datetime']+'</option>';
          $("select[name='exchanged_calendar_id']").append(_option);
        });
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー");
    }
  );
}
</script>
