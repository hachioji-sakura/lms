<div class="col-12">
  <div class="form-group">
    <label for="student_type" class="w-100">
      生徒の属性を選択してください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <label class="mx-2">
      <input type="checkbox" value="disabled" name="student_type[]" class="icheck flat-grey"
        onChange="tag_change(this);"
      @if(isset($item) && $item->user->has_tag('student_type', 'disabled')===true)
      checked
      @endif
       >タグ無し
    </label>
    @foreach($attributes['student_type'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="student_type[]" class="icheck flat-green"
      @if(isset($item) && $item->user->has_tag('student_type', $index)===true)
      checked
      @endif
       >{{$name}}
    </label>
    @endforeach
  </div>
</div>
<script>
function tag_change(obj){
  var _name = $(obj).attr("name");
  var _val = $(obj).val();
  var _checked = $(obj).prop("checked");
  console.log('tag_change');
  if(_checked && _val=='disabled'){
    //個別時間帯をすべてdisabled
    $('input[type="checkbox"][name="'+_name+'"][value!="disabled"]').each(function(i, e){
      if($(e).attr("value") !== "disabled") {
        $(this).prop('disabled', true);
        $(this).iCheck('uncheck');
        $(this).iCheck('disable');
      }
    });
  }
  else if(!_checked && _val=='disabled'){
    $('input[type="checkbox"][name="'+_name+'"][value!="disabled"]').each(function(i, e){
      if($(e).attr("value") !== "disabled"){
        $(this).parent().removeClass('disabled');
        $(this).prop('disabled', false);
        //$(this).iCheck('check');
        $(this).iCheck('enable');
      }
    });
  }
}
</script>
