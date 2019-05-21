@if(isset($_edit) && $_edit==true)
<div class="col-12">
  <div class="form-group">
    <label for="start_date" class="w-100">
      予定タイプ
    </label>
    <span title="{{$item->user_calendar_setting_id}}">
      {{$item->add_type_name()}}
    </span>
  </div>
</div>
@else
<div class="col-12 mb-1 student_selected collapse">
  <div class="form-group">
    <label for="add_type">
      振替授業
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group" id="add_type_form">
      <div class="form-check">
          <input class="form-check-input icheck flat-green" type="radio" name="add_type" id="add_type_add" value="add" required="true" onChange="add_type_change()" checked>
          <label class="form-check-label" for="add_type_add">
              追加
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="add_type" id="add_type_exchange" value="exchange" required="true" onChange="add_type_change()" validate="exchange_validate();">
          <label class="form-check-label" for="add_type_exchange">
              振替
          </label>
      </div>
    </div>
  </div>
</div>
<div class="col-12 collapse" id="exchanged_calendar">
  <div class="form-group">
    <label for="exchanged_calendar_id" class="w-100">
      振替対象の授業
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="exchanged_calendar_id" class="form-control" placeholder="振替対象の授業" reqired="true">
    </select>
  </div>
  <div id="exchanged_calendar_none" class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
</div>
<script>
function add_type_change(obj){
  var is_exchange = $('input[type="radio"][name="add_type"][value="exchange"]').prop("checked");
  $('select[name="exchanged_calendar_id"]').html('');
  if(is_exchange){
    //振替
    if(pre_exchange_validate()===true){
      $("select[name='exchanged_calendar_id']").show();
      get_exchange_calendar();
    }
  }
  else {
    //追加
    $("select[name='exchanged_calendar_id']").hide();
    $("#exchanged_calendar").collapse("hide");
  }

}
function get_exchange_calendar(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  var student_id = $('select[name="student_id[]"]').val()|0;
  console.log("get_exchange_calendar");
  //振替対象の予定を取得
  service.getAjax(false, '/api_calendars?teacher_id='+teacher_id+'&student_id='+student_id+'&exchange_target=1', null,
    function(result, st, xhr) {
      console.log(result["data"]);
      if(result['status']===200){
        var c = 0;
        $.each(result['data'], function(id, val){
          var _option = '<option value="'+val['id']+'">'+val['datetime']+'</option>';
          $("select[name='exchanged_calendar_id']").append(_option);
          c++;
        });
        if(c>0){
          $('select[name=exchanged_calendar_id]').show();
          $("#exchanged_calendar_none").hide();
        }
        else {
          $('select[name=exchanged_calendar_id]').hide();
          $("#exchanged_calendar_none").show();
        }
        $("#exchanged_calendar").collapse("show");
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー");
    }
  );
}
function pre_exchange_validate(){
  var is_exchange = $('input[type="radio"][name="add_type"][value="exchange"]').prop("checked");
  var course_type = $('input[type="radio"][name="course_type"]:checked').val();
  var student_count = $("select[name='student_id[]'] option:selected").length;
  front.clearValidateError('add_type_form');
  if(is_exchange){
    if(course_type=="group"){
      front.showValidateError('#add_type_form', 'グループレッスンの振替はできません');
      return false;
    }
    if(course_type=="single" && student_count>1){
      front.showValidateError('#add_type_form', '振替対象の生徒を一人にしてください');
      return false;
    }
  }
  return true;
}
function exchange_validate(){
  var is_exchange = $('input[type="radio"][name="add_type"][value="exchange"]').prop("checked");
  if(is_exchange){
    if(pre_exchange_validate()==false) return false;
    var _exchanged_calendar_id = $('select[name=exchanged_calendar_id]').val()|0;
    if(_exchanged_calendar_id > 0) return true;
    front.showValidateError('#add_type_form', '振替対象の授業を選択してください');
    return false;
  }
  return true;
}

</script>
@endif
