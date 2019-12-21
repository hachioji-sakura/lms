<div class="col-12 teacher_id_selected">
  <div class="form-group">
    <label for="title" class="w-100">
      生徒
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="student_id[]" class="form-control select2" multiple="multiple" width=100% placeholder="{{__('labels.charge_student')}}" required="true">
      <option value="">{{__('labels.selectable')}}</option>
    </select>
    @if(isset($_edit) && $_edit==true)
    <select name="__student_id[]" class="hide" multiple="multiple" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($item->teacher->get_charge_students() as $student)
         <option
         value="{{ $student->id }}"
         grade="{{$student->tag_value('grade')}}"
         @if($item->is_member($student->id)===true) selected @endif
         >{{$student->name()}}</option>
      @endforeach
    </select>
    @endif  </div>
</div>
<script>
$(function(){
  teacher_id_change();
});
function teacher_id_change(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  console.log("teacher_id_change:"+teacher_id);
  if(teacher_id > 0){
    $(".teacher_id_selected").collapse("show");
    get_charge_students();
  }
  else {
    $(".teacher_id_selected").collapse("hide");
  }
}
function get_charge_students(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  console.log("get_charge_students");
  //対象の生徒を取得
  service.getAjax(false, '/teachers/'+teacher_id+'/students', {'loading': false},
    function(result, st, xhr) {
      if(result['status']===200){
        var c = 0;
        var student_id_form = $("select[name='student_id[]']");
        student_id_form.select2('destroy');
        console.log(result['data']);
        student_id_form.empty();
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
        var _width = student_id_form.attr("width");
        student_id_form.select2({
          width: _width,
          placeholder: '選択してください',
        });
        if($("select[name='__student_id[]']").length > 0){
          var val = $("select[name='__student_id[]']").val();
          student_id_form.val(val).trigger('change');
        }
        else {
          student_id_form.val(-1).trigger('change');
        }
        student_id_form.show();
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー(student_groups.get_charge_students)");
    }
  );
}
</script>
