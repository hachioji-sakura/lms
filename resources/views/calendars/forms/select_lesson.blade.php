@if(count($teacher->get_tags('lesson'))>1)
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="course_type" class="w-100">
      レッスン
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group">
      @foreach($teacher->get_tags('lesson') as $lesson)
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="lesson" id="lesson_{{$lesson["value"]}}" value="{{$lesson["value"]}}" required="true" onChange="lesson_change()"
            @if($loop->index===0)
             checked
            @endif
            ><label class="form-check-label" for="lesson_{{$lesson["value"]}}">{{$lesson["name"]}}</label>
        </div>
      @endforeach
    </div>
  </div>
</div>
@else
{{-- レッスンが１つしかない --}}
<input type="hidden" name="lesson" value="{{$teacher->get_tag('lesson')['value']}}">
@endif
<script>
$(function(){
  lesson_change();
  get_charge_students();
});
function lesson_change(){
  var lesson = ($('input[name=lesson]').val())|0;
  $(".charge_subject").hide();
  $("#course_type_form .form-check").hide();
  $("#course_type_form_single").show();
  $("#course_type_form_family").show();
  $(".charge_subject_"+lesson).show();
  switch(lesson){
    case 2:
    case 4:
      $("#course_type_form_group").show();
      break;
  }
  $(".lesson_selected").collapse('show');
  //担当生徒取得
}
function get_charge_students(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  var lesson = ($('input[name=lesson]').val())|0;
  console.log("get_charge_students");
  //振替対象の予定を取得
  service.getAjax(false, '/teachers/'+teacher_id+'/students?lesson='+lesson, null,
    function(result, st, xhr) {
      if(result['status']===200){
        var c = 0;
        var student_id_form = $("select[name='student_id[]']");
        student_id_form.select2('destroy');
        $.each(result['data'], function(id, val){
          var _option = '<option value="'+val['id']+'"';
          var _field = ['grade'];
          for(var i=0,n=_field.length;i<n;i++){
            _option += ' '+_field[i]+'="'+val[_field[i]]+'"';
          }
          _option+= '>'+val['name']+'</option>';
          student_id_form.append(_option);
          c++;
        });
        if(c>0){
          var _width = student_id_form.attr("width");
          student_id_form.select2({
            width: _width,
            placeholder: '選択してください',
          });
          student_id_form.val(-1).trigger('change');
          student_id_form.show();
          $("#select_student_none").hide();
        }
        else {
          student_id_form.hide();
          $("#select_student_none").show();
        }
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー");
    }
  );
}
</script>
