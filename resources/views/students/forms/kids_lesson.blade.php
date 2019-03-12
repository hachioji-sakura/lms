<div class="col-12 kids_lesson_form">
  <div class="form-group">
    <label for="howto" class="w-100">
      習い事の内容についてお知らせください
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['kids_lesson'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="kids_lesson[]" class="icheck flat-green" >{{$name}}
    </label>
    @endforeach
  </div>
</div>
