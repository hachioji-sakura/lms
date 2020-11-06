@if(count($teacher->get_tags('lesson'))>1 && $_edit==false)
<div class="col-12 mt-2 schedule_type schedule_type_class schedule_type_other">
  <div class="form-group">
    <label for="course_type" class="w-100">
      レッスン
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group">
      @foreach(config('attribute.lesson') as $key => $name)
        @if($teacher->has_tag('lesson', $key)!=true) @continue @endif
        <div class="form-check">
            <input class="form-check-input icheck flat-green" type="radio" name="lesson" id="lesson_{{$key}}" value="{{$key}}" alt="{{$name}}" required="true" onChange="lesson_change()"
            @if($item["exchanged_calendar_id"]>0 && $key==$item->lesson(true))
             checked
            @elseif($loop->index===0)
             checked
            @endif
            ><label class="form-check-label" for="lesson_{{$key}}">{{$name}}</label>
        </div>
      @endforeach
    </div>
  </div>
</div>
@elseif($_edit==true)
{{-- レッスンは編集不可のため、編集時はすでに登録済みのレッスンが１つしかない --}}
<input type="hidden" name="lesson" value="{{$item->lesson(true)}}" >
@elseif(isset($teacher))
{{-- レッスンが１つしかない --}}
<input type="hidden" name="lesson" value="{{$teacher->get_tag('lesson')['value']}}" alt="{{$teacher->get_tag('lesson')['name']}}">
@endif
