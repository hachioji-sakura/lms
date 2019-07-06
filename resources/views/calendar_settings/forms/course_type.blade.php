{{-- 塾場合マンツー or ファミリー --}}
@if($select_lesson==1 && $item->has_tag("course_type", "family"))
<input type="hidden" name="course_type" value="family">
@elseif($select_lesson==1)
<input type="hidden" name="course_type" value="single">
@elseif($select_lesson==3)
{{-- ピアノの場合マンツーマンのみ --}}
<input type="hidden" name="course_type" value="single">
@else
<div class="col-12 mt-2">
  <div class="form-group">
    <label for="course_type" class="w-100">
      {{__('labels.lesson_type')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <div class="input-group" id="course_type_form">
      <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_single" value="single" required="true"
      @if($item->has_tag("course_type", "single"))
      checked
      @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['english_talk_course_type']) && isset($item["tagdata"]['english_talk_course_type']['single']))
      checked
      @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['kids_lesson_course_type']) && isset($item["tagdata"]['kids_lesson_course_type']['single']))
      checked
      @endif
      >
      <label class="form-check-label mr-3" for="course_type_single">
          {{__('labels.one_to_one')}}
      </label>
      <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_group" value="group" required="true"
      @if($item->has_tag("course_type", "group"))
      checked
      @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['english_talk_course_type']) && isset($item["tagdata"]['english_talk_course_type']['group']))
      checked
      @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['kids_lesson_course_type']) && isset($item["tagdata"]['kids_lesson_course_type']['group']))
      checked
      @endif
      >
      <label class="form-check-label mr-3" for="course_type_group">
          {{__('labels.group')}}
      </label>
      {{--
      <input class="form-check-input icheck flat-green ml-3" type="radio" name="course_type" id="course_type_family" value="family" required="true"
      @if($item->has_tag("course_type", "family"))
      checked
      @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['course_type']) && isset($item["tagdata"]['course_type']['family']))
      checked
      @endif
      >
      <label class="form-check-label mr-3" for="course_type_family">
          {{__('labels.family')}}
      </label>
      --}}
    </div>
  </div>
</div>
@endif
