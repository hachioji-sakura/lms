<div class="col-12 mt-2">
  <div class="form-group">
    <label for="course_minutes" class="w-100">
      授業時間
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['course_minutes'] as $index => $name)
    <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green"
        @if($item->has_tag("course_minutes", $index))
        checked
        @endif
      required="true">{{$name}}
    </label>
    @endforeach
  </div>
</div>
