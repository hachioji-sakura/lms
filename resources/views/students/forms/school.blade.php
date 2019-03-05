<div class="col-12 col-lg-3 col-md-3">
  <div class="form-group">
    <label for="grade" class="w-100">
      学年
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="grade" class="form-control" placeholder="学年" required="true" onChange="grade_select_change()" accesskey="grade" value="{{$item->get_tag('grade')['value']}}">
      <option value="">(選択してください)</option>
      @foreach($attributes['grade'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 col-lg-9 col-md-9 collapse school_name_form">
  <div class="form-group">
    <label for="school_name" class="w-100">
      学校名
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    <input type="text" id="school_name" name="school_name" class="form-control" placeholder="例：八王子市立サクラ中学校" value="{{$item->get_tag('school_name')['value']}}">
  </div>
</div>
<script>
function grade_select_change(obj){
  console.log("grade_select_change");
  var grade_name = $('select[name=grade] option:selected').text().trim();
  $(".grade-subject").hide();
  var is_school_name_show = false;
  if(grade_name.substring(0,1)=="高"){
    $(".grade-subject[alt='高校']").show();
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="中"){
    $(".grade-subject[alt='中学']").show();
    is_school_name_show = true;
  }
  else if(grade_name.substring(0,1)=="小"){
    $(".grade-subject[alt='小学']").show();
    is_school_name_show = true;
  }
  if(is_school_name_show){
    $(".school_name_form").collapse("show");
    $(".school_name_confirm").collapse("show");
  }
  else {
    $(".school_name_form").collapse("hide");
    $(".school_name_confirm").collapse("hide");
  }
}
</script>
