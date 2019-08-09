<div class="col-12">
    <label for="course_minutes" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      {{__('labels.lesson_time')}}
      @else
      1回の授業時間は何分をご希望でしょうか？
      @endif
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @foreach($attributes['course_minutes'] as $index => $name)
      <label class="mx-2 course_minutes" for="course_minutes_{{$index}}">
        <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green"
        @if(isset($item) && isset($item->id) && $item->has_tag("course_minutes", $index))
        checked
        @endif
        id="course_minutes_{{$index}}"
      required="true">
          {{$name}}
        </label>
    @endforeach
</div>
@if(isset($_teacher) && $_teacher===true)
@else
<div class="col-12">
  <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
    ※塾の授業時間は60分～となります<br>
    英会話・ピアノ・習い事につきまして、授業時間は30分、もしくは60分となります。
  </h6>
</div>
@endif
