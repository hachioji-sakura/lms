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
function select_student_change(){
  var options = {};
  $("select[name='student_id[]'] option:selected").each(function(){
    var val = $(this).val();
    var grade = $(this).attr("grade");
    var grade_code = "";
    if(!util.isEmpty(grade)){
      grade_code = grade.substr(0,1);
    }
    $("select[name='__charge_subject[]'] option[grade='"+grade_code+"']").each(function(){
      options[$(this).val()] = $(this).text();
    });
    console.log(val+":"+grade_code);
  });
  var _options = [];
  $.each(options, function(i, v){
    _options.push({'id':i, 'text':v});
  });
  var charge_subject_form = $("select[name='charge_subject[]']");
  var _width = charge_subject_form.attr("width");
  charge_subject_form.select2('destroy');
  charge_subject_form.empty();
  charge_subject_form.select2({
    data : _options,
    width: _width,
    placeholder: '選択してください',
  });
  $(".student_selected").collapse('show');
  $('input[type="radio"][name="add_type"]:checked').change();
}
</script>
