<div class="col-12">
  <div class="form-group">
    <label for="course_minutes" class="w-100">
      1回の授業時間は何分をご希望でしょうか？
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['course_minutes'] as $index => $name)
    <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green" >{{$name}}
    </label>
    @endforeach
  </div>
</div>
