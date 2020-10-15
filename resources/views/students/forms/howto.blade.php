<div class="col-12">
  <div class="form-group">
    <label for="howto" class="w-100">
      当塾をお知りになった方法は何でしょうか？
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($attributes['howto'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="howto[]" class="icheck flat-green"  onChange="howto_checkbox_change(this)"
      @if($_edit==true && $item->has_tag('howto', $index)==true) checked @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
<div class="col-12 collapse howto_word_form">
  <div class="form-group">
    <label for="howto_word" class="w-100">
      Google検索 / Yahoo検索をお答えの方、検索ワードを教えてください。
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" id="howto_word" name="howto_word" class="form-control" placeholder="例：八王子 学習塾" >
  </div>
</div>
