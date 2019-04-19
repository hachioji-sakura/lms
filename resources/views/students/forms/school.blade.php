<div class="col-12 col-lg-3 col-md-3">
  <div class="form-group">
    <label for="{{$prefix}}grade" class="w-100">
      学年
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="{{$prefix}}grade" class="form-control" placeholder="学年" required="true" onChange="{{$prefix}}grade_select_change(this)" accesskey="{{$prefix}}grade" @if(isset($item) && !empty($item)) value="{{$item->get_tag('grade')['value']}}" @endif>
      <option value="">(選択してください)</option>
      @foreach($attributes['grade'] as $index => $name)
        <option value="{{$index}}"
        @if(isset($item) && !empty($item) && $index==$item->get_tag('grade')['value']) selected @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 col-lg-9 col-md-9 collapse {{$prefix}}school_name_form">
  <div class="form-group">
    <label for="{{$prefix}}school_name" class="w-100">
      学校名
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    <input type="text" id="{{$prefix}}school_name" name="{{$prefix}}school_name" class="form-control" placeholder="例：八王子市立サクラ中学校"
      @if(isset($item) && !empty($item)) value="{{$item->get_tag('school_name')['value']}}" @endif
      >
  </div>
</div>
<script>
function {{$prefix}}grade_select_change(obj){
  var _name = $(obj).attr('name');
  var grade_name = $('select[name="'+_name+'"] option:selected').text().trim();
  var is_school_name_show = false;
  var _grade_name = "";
  if(grade_name.substring(0,1)=="高"){
    _grade_name = "高校";
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="中"){
    _grade_name = "中学";
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="小"){
    _grade_name = "小学";
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="大"){
    _grade_name = "高校";
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="成"){
    _grade_name = "高校";
  }
  console.log("{{$prefix}}grade_select_change["+_name+"]:"+grade_name+":"+is_school_name_show);
  @isset($noscript)
  @else
  $(".grade-subject").hide();
  $(".grade-subject[alt='"+_grade_name+"']").show();
  @endif
  if(is_school_name_show){
    $(".{{$prefix}}school_name_form").collapse("show");
    $(".{{$prefix}}school_name_confirm").collapse("show");
  }
  else {
    $(".{{$prefix}}school_name_form").collapse("hide");
    $(".{{$prefix}}school_name_confirm").collapse("hide");
  }
}
</script>
