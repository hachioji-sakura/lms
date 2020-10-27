<div class="col-12">
  <div class="form-group">
    <label for="entry_milestone" class="w-100">
      特に重視してやって欲しいことをお知らせください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($attributes['entry_milestone'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="entry_milestone[]" class="icheck flat-green"  onChange="entry_milestone_checkbox_change(this)"
      @if($_edit==true && $item->has_tag('entry_milestone', $index)==true) checked @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
<div class="col-12 collapse entry_milestone_word_form">
  <div class="form-group">
    <label for="entry_milestone_word" class="w-100">
      その他とお答えいただいた場合、内容をご記入ください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" id="entry_milestone_word" name="entry_milestone_word" class="form-control" placeholder="例：学習習慣を身に付けたい" >
  </div>
</div>
<script>
function entry_milestone_checkbox_change(obj){
  var is_other = $('input[type="checkbox"][name="entry_milestone[]"][value="other"]').prop("checked");
  if(is_other){
    $(".entry_milestone_word_form").collapse("show");
    $(".entry_milestone_word_confirm").collapse("show");
  }
  else {
    $(".entry_milestone_word_form").collapse("hide");
    $(".entry_milestone_word_confirm").collapse("hide");
  }
}
</script>
