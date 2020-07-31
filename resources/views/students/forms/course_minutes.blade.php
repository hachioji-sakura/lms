@if(isset($_teacher) && $_teacher===true && $_edit==true && $item["course_minutes"]>0)
<div class="col-12 schedule_type schedule_type_class mb-2">
    <label for="course_minutes" class="w-100">
      {{__('labels.lesson_time')}}
    </label>
    <input type="hidden" name="course_minutes" value="{{$item["course_minutes"]}}">
    <input type="hidden" name="course_minutes_name" value="{{$attributes['course_minutes'][$item["course_minutes"]]}}">
    <span>{{$attributes['course_minutes'][$item["course_minutes"]]}}</span>
</div>
@else
  <div class="col-12 schedule_type schedule_type_class">
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
          @if(isset($item["exchanged_calendar_id"]) && $item["exchanged_calendar_id"] > 0 && $item->get_exchange_remaining_time() < intval($index))
            @continue
          @endif
          <input type="radio" value="{{ $index }}" name="course_minutes" class="icheck flat-green"
          @if(isset($item) && isset($item->id) && $item->has_tag("course_minutes", $index))
          checked
          @elseif(!empty($item) && isset($item["course_minutes"]) && $index==$item["course_minutes"])
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
@endif
