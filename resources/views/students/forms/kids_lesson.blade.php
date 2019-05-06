<div class="col-12 kids_lesson_form">
  <div class="form-group">
    <label for="howto" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      担当可能な習い事につきまして
      @else
      ご希望の習い事につきましてお知らせください
      @endif
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['kids_lesson'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="kids_lesson[]" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag('kids_lesson', $index)===true)
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
