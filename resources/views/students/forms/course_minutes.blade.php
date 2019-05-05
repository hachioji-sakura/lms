<div class="col-12">
  <div class="form-group">
    <label for="course_minutes" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      授業時間
      @else
      1回の授業時間は何分をご希望でしょうか？
      @endif
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['course_minutes'] as $index => $name)
    <label class="mx-2">
      <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green"
        @if($_edit===true && isset($item) && $item->has_tag("course_minutes"))
        checked
        @endif
      required="true">{{$name}}
    </label>
    @endforeach
  </div>
</div>
