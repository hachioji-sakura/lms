<div class="col-12 course_type_form">
  <div class="form-group">
    <label for="course_type" class="w-100">
      授業形式のご希望をお知らせください
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    @foreach($attributes['course_type'] as $index => $name)
    <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="course_type" class="icheck flat-green" >{{$name}}
    </label>
    @endforeach
  </div>
</div>
