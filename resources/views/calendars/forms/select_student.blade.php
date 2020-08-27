@if($item->trial_id>0)
<div class="col-12 lesson_selected collapse schedule_type schedule_type_other schedule_type_class">
  <div class="form-group">
    <label for="title" class="w-100">
      {{__('labels.students')}}
    </label>
    <span>{{$item->trial->student->name()}}</span>
      <input type="hidden" name="student_id[]"
        value="{{$item->trial->student->id}}"
        grade="{{$item->trial->student->tag_value('grade')}}"
        >
  </div>
  <input type="hidden" name="student_name" value="{{$item->trial->student->name()}}">
</div>
@elseif((isset($item["exchanged_calendar_id"]) && $item["exchanged_calendar_id"]) > 0 || (isset($_edit) && $_edit==true))
<div class="col-12 lesson_selected collapse schedule_type schedule_type_other schedule_type_class">
  <div class="form-group">
    <label for="title" class="w-100">
      {{__('labels.students')}}
    </label>
    <span>{{$item['student_name']}}</span>
    @foreach($item->get_students() as $member)
      <input type="hidden" name="student_id[]"
        value="{{$member->user->details('students')->id}}"
        grade="{{$member->user->details('students')->tag_value('grade')}}"
        >
    @endforeach
  </div>
  <input type="hidden" name="student_name" value="{{$item['student_name']}}">
</div>
@elseif(!(isset($_edit) && $_edit==true))
<div class="col-12 lesson_selected collapse schedule_type schedule_type_other schedule_type_class">
  <div class="form-group">
    <label for="title" class="w-100">
      {{__('labels.students')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="student_id[]" class="form-control select2" multiple="multiple" width=100% placeholder="{{__('labels.charge_student')}}" required="true" onChange="select_student_change()">
      <option value="">{{__('labels.selectable')}}</option>
    </select>
    @if((isset($_edit) && $_edit==true) || isset($item['exchanged_calendar_id']) && $item['exchanged_calendar_id']>0)
      @foreach($item->get_students() as $member)
        <input type="hidden" name="select_student_id[]"
          value="{{$member->user->details('students')->id}}"
          grade="{{$member->user->details('students')->tag_value('grade')}}"
          >
      @endforeach
    @endif
  </div>
  <div id="select_student_none" class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
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
        student_id_form.empty();
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

        course_type_change();
        var select_student_id_form = $("input[name='select_student_id[]']");
        var select_student_ids = [];
        if(select_student_id_form.length > 0){
          select_student_id_form.each(function(index, element){
            select_student_ids.push($(element).val());
          });
          student_id_form.val(select_student_ids).trigger('change');
        }
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー(calendars.get_charge_students)");
    }
  );
}
</script>
@endif
