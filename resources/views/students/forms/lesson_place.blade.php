<div class="col-12">
  <div class="form-group">
    <label for="lesson_place" class="w-100">
      ご希望の校舎
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['lesson_place'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="lesson_place[]" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag("lesson_place", $index))
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
