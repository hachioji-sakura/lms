@if($item["exchanged_calendar_id"] > 0)
<input type="hidden" name="course_type" value="{{$item->get_tag_value('course_type')}}" >
<input type="hidden" name="course_type_name" value="{{$item->get_tag_name('course_type')}}" >
@elseif($_edit==true )
<input type="hidden" name="course_type" value="{{$item->get_tag_value('course_type')}}" >
<input type="hidden" name="course_type_name" value="{{$item->get_tag_name('course_type')}}" >
<div class="col-12 mt-2 schedule_type schedule_type_class">
  <div class="form-group">
    <label for="course_type" class="w-100">
      {{__('labels.lesson_type')}}
    </label>
    <span>{{$item->get_tag_name('course_type')}}</span>
  </div>
</div>
@else
<div class="col-12 mt-2 schedule_type schedule_type_class">
  <div class="form-group">
    <label for="course_type" class="w-100">
      {{__('labels.lesson_type')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" id="course_type_form">
      <div class="form-check" id="course_type_form_single">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_single" value="single" required="true" onChange="course_type_change()"
          @if(isset($_edit) && $_edit==true && $item->has_tag('course_type', 'single'))
            checked
          @else(!isset($_edit) || $_edit!=true)
            checked
          @endif
          >
          <label class="form-check-label" for="course_type_single">
            {{__('labels.one_to_one')}}
          </label>
      </div>
      <div class="form-check ml-2" id="course_type_form_group">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_group" value="group" required="true" onChange="course_type_change()"
          @if(isset($_edit) && $_edit==true && $item->has_tag('course_type', 'group'))
          checked
          @endif
          >
          <label class="form-check-label" for="course_type_group">
            {{__('labels.group')}}
          </label>
      </div>
      <div class="form-check ml-2" id="course_type_form_family">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_family" value="family" required="true" onChange="course_type_change()"
          @if(isset($_edit) && $_edit==true && $item->has_tag('course_type', 'family'))
          checked
          @endif
          >
          <label class="form-check-label" for="course_type_family">
            {{__('labels.family')}}
          </label>
      </div>
    </div>
  </div>
</div>
@endif
<script>
function course_type_change(){
  var course_type = $('input[type="radio"][name="course_type"]:checked').val();
  if(!course_type){
    course_type = $('input[type="hidden"][name="course_type"]').val();
  }
  if(!course_type){
    return false;
  }
  console.log('course_type_change:'+course_type);
  if($("select[name='student_id[]']").length>0){
    var student_id_form = $("select[name='student_id[]']");
    var _width = student_id_form.attr("width");
    student_id_form.select2('destroy');
    student_id_form.removeAttr("multiple");
    if(course_type!=="single" && student_id_form.attr('multiple')!='multiple'){
      //グループ or ファミリーの場合
      get_student_group();
      student_id_form.attr("multiple", "multiple");
      console.log('course_type_change:'+course_type);
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
