@if(isset($_edit) && $_edit==true && isset($item))
<div class="col-12 mb-2">
  <label for="season_lesson_course" class="w-100">
    {{__('labels.lesson_time')}}
  </label>
  <input type="hidden" name="season_lesson_course" value="{{$item["season_lesson_course"]}}">
  <input type="hidden" name="season_lesson_course_name" value="{{$attributes['season_lesson_course'][$item["season_lesson_course"]]}}">
  <span>{{$attributes['season_lesson_course'][$item["season_lesson_course"]]}}</span>
</div>
@else
<div class="col-12 mb-2">
    <label for="season_lesson_course" class="w-100">
      ご希望のコース
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['season_lesson_course'] as $index => $name)
      <label class="mx-2 season_lesson_course" for="season_lesson_course_{{$index}}">
        <input type="radio" value="{{ $index }}" name="season_lesson_course" class="icheck flat-green"
        @if(isset($item) && isset($item->id) && $item->has_tag("season_lesson_course", $index))
        checked
        @elseif(!empty($item) && isset($item["season_lesson_course"]) && $index==$item["season_lesson_course"])
        checked
        @endif
        id="season_lesson_course_{{$index}}"
      required="true">
          {{$name}}
        </label>
    @endforeach
</div>
@endif
