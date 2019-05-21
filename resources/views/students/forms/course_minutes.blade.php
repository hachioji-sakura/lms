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
    <div class="input-group" id="">
    @foreach($attributes['course_minutes'] as $index => $name)
      <div class="form-check">
        <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green"
        @if(isset($_edit) && $_edit==true && isset($item) && $item->has_tag("course_minutes", $index))
        checked
        @elseif(isset($item) && isset($item['course_minutes']) && $item['course_minutes'] == $index)
        checked
        @endif
        id="course_minutes_{{$index}}"
      required="true">
        <label class="form-check-label" for="course_minutes_{{$index}}">
          {{$name}}
        </label>
      </div>
    @endforeach
    </div>
  </div>
</div>
