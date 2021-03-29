<div class="col-12 schedule_type schedule_type_other">
  <div class="form-group">
    <label for='work' class="w-100">
      {{__('labels.work')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-wrench"></i></span>
      </div>
      <select name='work' class="form-control" required="true" onChange="work_change()">
        @foreach($attributes['work'] as $index=>$name)
        @if(intval($index)>5) @continue @endif
        <option value="{{ $index }}" @if($item['work']==$index) selected @endif>{{$name}}
        </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
<script>
$(function(){
  work_change();
});
function work_change(){

  var work = $('select[name="work"]').val();
  if(!work){
    return false;
  }
  if($("select[name='student_id[]']").length>0){
    var student_id_form = $("select[name='student_id[]']");
    var _width = student_id_form.attr("width");
    student_id_form.select2('destroy');
    student_id_form.removeAttr("multiple");
    //TODO work=5 は演習、The Magic Number Logic
    if(work==5){
      //グループ or ファミリーの場合
      student_id_form.attr("multiple", "multiple");
      $(".course_type_selected").collapse('show');
    }
    else {
      $(".course_type_selected").collapse('hide');
    }
    student_id_form.select2({
      width: _width,
      placeholder: '選択してください',
    });
    student_id_form.val(-1).trigger('change');
  }
}

</script>
