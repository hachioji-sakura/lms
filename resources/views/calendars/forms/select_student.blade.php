@if(isset($_edit) && $_edit==true)
<div class="col-12 lesson_selected collapse">
  <div class="form-group">
    <label for="title" class="w-100">
      生徒
    </label>
    @foreach($item->students as $member)
    <a href="/students/{{$member->user->details('students')->id}}" class="mr-2" target=_blank>
      <i class="fa fa-user-graduate"></i>
      {{$member->user->details('students')->name}}
    </a>
    <input type="hidden" name="student_id[]"
      value="{{$member->user->details('students')->id}}"
      grade="{{$member->user->details('students')->tag_value('grade')}}"
      >
    @endforeach
  </div>
</div>
@else
<div class="col-12 lesson_selected collapse">
  <div class="form-group">
    <label for="title" class="w-100">
      生徒
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="student_id[]" class="form-control select2" multiple="multiple" width=100% placeholder="担当生徒" required="true" onChange="select_student_change()">
      <option value="">(選択)</option>
      {{--
      @foreach($items as $student)
         <option
         value="{{ $student->id }}"
         grade="{{$student->tag_value('grade')}}"
         @if(isset($_edit) && $item['student_id'] == $student->id) selected @endif
         >{{$student->name()}}</option>
      @endforeach
      --}}
    </select>
  </div>
  <div id="select_student_none" class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
</div>
<script>
$(function(){
  get_charge_students();
});
function get_charge_students(){
  var teacher_id = ($('*[name=teacher_id]').val())|0;
  var lesson = ($('input[name=lesson]').val())|0;
  console.log("get_charge_students");
  //対象の生徒を取得
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
@endif
