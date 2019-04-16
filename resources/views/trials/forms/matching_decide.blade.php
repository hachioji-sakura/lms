<div class="col-12">
  <div class="form-group">
    <label for="matching_decide" class="w-100">
      講師を決めた理由は？
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['matching_decide'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="matching_decide[]" class="icheck flat-green"  onChange="matching_decide_checkbox_change(this)" required="true">{{$name}}
    </label>
    @endforeach
  </div>
</div>
<div class="col-12 collapse matching_decide_word_form">
  <div class="form-group">
    <label for="matching_decide_word" class="w-100">
      その他の場合、理由を記述してください
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="matching_decide_word" name="matching_decide_word" class="form-control" placeholder="例：数学の受験対策を希望していたため" >
  </div>
</div>
<script>
function matching_decide_checkbox_change(obj){
  var is_other = $('input[type="checkbox"][name="matching_decide[]"][value="other"]').prop("checked");
  if(is_other){
    $(".matching_decide_word_form").collapse("show");
    $(".matching_decide_word_confirm").collapse("show");
  }
  else {
    $(".matching_decide_word_form").collapse("hide");
    $(".matching_decide_word_confirm").collapse("hide");
  }
}
</script>
