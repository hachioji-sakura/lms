<div class="col-12 col-lg-3 col-md-3">
  <div class="form-group">
    <label for="{{$prefix}}grade" class="w-100">
      {{__('labels.grade')}}
      @if(!(isset($is_label) && $is_label==true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label==true)
      @if(isset($item) && !empty($item->grade()))
      <span>
        {{$item->grade()}}
      </span>
      <input type="hidden" name="{{$prefix}}grade_name" value="{{$item->grade()}}">
      <input type="hidden" class="grade" name="{{$prefix}}grade" value="{{$item->tag_value('grade')}}">
      @endif
    @else
    <select name="{{$prefix}}grade" class="form-control grade" placeholder="学年" required="true" onChange="grade_select_change()" accesskey="{{$prefix}}grade" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['grade'] as $index => $name)
        <option value="{{$index}}"
        @if(isset($item) && !empty($item) && $index==$item->tag_value('grade')) selected @endif
        >{{$name}}</option>
      @endforeach
    </select>
    @endif
  </div>
</div>
<div class="col-12 col-lg-9 col-md-9 {{$prefix}}grade_school_name_form">
  <div class="form-group">
    <label for="{{$prefix}}school_name" class="w-100">
      {{__('labels.school_name')}}
      @if(!(isset($is_label) && $is_label==true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label==true)
      @if(isset($item) && !empty($item->school_name()))
      <span>
        {{$item->school_name()}}
      </span>
      @endif
    @else
    <input type="text" id="{{$prefix}}school_name" name="{{$prefix}}school_name" class="form-control" required="true" placeholder="例：八王子市立サクラ中学校"
      @if(isset($item) && !empty($item->tag_value('school_name'))) value="{{$item->tag_value('school_name')}}" @endif
      >
    @endif
  </div>
</div>
<script>
$(function(){
  grade_select_change();
});
function grade_select_change(){
  console.log("grade_select_change");
  $(".grade-subject").hide();
  if($('select.grade').length > 0){
    $('select.grade').each(function(index, element){
      var _name = $(this).attr('name');
      var grade_name = $('select[name="'+_name+'"] option:selected').text().trim();
      if(is_school(grade_name)){
        $("."+_name+"_school_name_form").collapse("show");
        $("."+_name+"_school_name_confirm").collapse("show");
      }
      else {
        $("."+_name+"_school_name_form").collapse("hide");
        $("."+_name+"_school_name_confirm").collapse("hide");
      }
      var subject_grade = get_subject_grade(grade_name);
      $(".grade-subject[alt='"+subject_grade+"']").show();
    });
  }
  else {
    if($('input.grade[type=hidden]').length > 0){
      $('input.grade[type=hidden]').each(function(index, element){
        var _name = $(this).attr('name');
        var grade_name = $('input[name="'+_name+'_name"]').val();
        if(is_school(grade_name)){
          $("."+_name+"_school_name_form").collapse("show");
          $("."+_name+"_school_name_confirm").collapse("show");
        }
        else {
          $("."+_name+"_school_name_form").collapse("hide");
          $("."+_name+"_school_name_confirm").collapse("hide");
        }
        var subject_grade = get_subject_grade(grade_name);
        $(".grade-subject[alt='"+subject_grade+"']").show();
      });
    }
  }
}
function is_school(grade_name){
  var ret = false;
  if(grade_name.substring(0,1)=="高"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="中"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="小"){
    ret = true;
  }
  else if(grade_name.substring(0,1)=="大"){
    ret = true;
  }
  return ret;
}
function get_subject_grade(grade_name){
  var _grade_name = "";
  if(grade_name.substring(0,1)=="高"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="中"){
    _grade_name = "中学";
  }
  else if(grade_name.substring(0,1)=="小"){
    _grade_name = "小学";
  }
  else if(grade_name.substring(0,1)=="大"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="成"){
    _grade_name = "高校";
  }
  else if(grade_name.substring(0,1)=="幼"){
    _grade_name = "小学";
  }
  return _grade_name;
}
</script>
