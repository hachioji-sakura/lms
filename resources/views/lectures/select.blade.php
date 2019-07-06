<div class="col-12" id="select_lesson_form">
  <div class="form-group">
    <label for="lesson" class="w-100">
      {{__('labels.lesson')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="lesson" class="form-control" placeholder="レッスン" required="true" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['lesson'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
    {{--
    <select name="lesson" class="form-control" placeholder="レッスン" required="true" onChange="select_lesson_change(this)">
      <option value="">{{__('labels.selectable')}}</option>
    </select>
    --}}
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="course" class="w-100">
      {{__('labels.lesson_type')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="course" class="form-control" placeholder="コース" required="true" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['course'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>

    {{--
    <select name="course" class="form-control" placeholder="コース" required="true" onChange="select_course_change(this)">
      <option value="">{{__('labels.selectable')}}</option>
    </select>
    --}}
  </div>
</div>
<div class="col-12">
  <div class="form-group">
    <label for="subject" class="w-100">
      {{__('labels.subject')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="subject" class="form-control" required="true" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['subject'] as $index => $name)
        <option value="{{$index}}">{{$name}}</option>
      @endforeach
    </select>
    {{--
    <select name="subject" class="form-control" required="true" >
    </select>
    --}}
  </div>
</div>
<script>
var lectures = null;
var _defaultOption = '<option value="">{{__('labels.selectable')}}</option>';
function select_lesson_set(){
  lectures = util.getLocalData('lectures');
  if(util.isEmpty(lectures)){
    get_lectures();
    return;
  }
  console.log("select_lesson_set");
  $("select[name='lesson']").html('');
  $("select[name='course']").html(_defaultOption);
  $("select[name='subject']").html(_defaultOption);
  var c = 0;
  $.each(lectures, function(id, val){
    var _option = '<option value="'+id+'">'+val["name"]+'</option>';
    $("select[name='lesson']").append(_option);
    c++;
  });
  $("select[name='lesson']").trigger('change');
  if(c==1) $("#select_lesson_form").css("display", "none");
}
function select_lesson_change(obj){
  if(util.isEmpty(lectures)) return;
  var _val = $(obj).val();
  if(util.isEmpty(_val)) return ;
  var _items = lectures[_val]['courses'];
  $("select[name='course']").html(_defaultOption);
  $("select[name='subject']").html(_defaultOption);
  $.each(_items, function(id, val){
    var _option = '<option value="'+id+'">'+val["name"]+'</option>';
    $("select[name='course']").append(_option);
  });
}
function select_course_change(obj){
  var l = $("select[name='lesson']").val();
  if(util.isEmpty(lectures)) return;
  var _val = $(obj).val();
  if(util.isEmpty(_val)) return ;
  $("select[name='subject']").html(_defaultOption);
  var _items = lectures[l]['courses'][_val]['subjects'];
  $.each(_items, function(id, name){
    var _option = '<option value="'+id+'">'+name+'</option>';
    $("select[name='subject']").append(_option);
  });
}
function get_lectures(){
  console.log("get_lectures");
  service.getAjax(false, '/api_lectures', null,
    function(result, st, xhr) {
      var lectures = {};
      if(result['status']===200){
        $.each(result['data'], function(index, value) {
          var l = value['lesson'];
          var c = value['course'];
          var s = value['subject'];
          if(!lectures[l]){
            lectures[l] = {
              "courses" : {},
              "name" : value['lesson_name']
            };
          }
          if(!lectures[l]["courses"][c]){
            lectures[l]["courses"][c] = {
              "subjects" : {},
              "name" : value['course_name']
            };
          }
          if(!lectures[l]["courses"][c]["subjects"][s]){
            lectures[l]["courses"][c]["subjects"][s] = value['subject_name'];
          }
        });
        util.setLocalData('lectures', lectures);
        select_lesson_set();
      }
    },
    function(xhr, st, err) {
        alert("UI取得エラー"+messageParam);
    }
  );
}
$(function(){
  //select_lesson_set();
});

</script>
