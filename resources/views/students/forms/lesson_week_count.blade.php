<div class="col-12">
  <div class="form-group">
    <label for="lesson_week_count" class="w-100">
      週何回の授業をご希望ですか？
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    @for($i=1;$i<6;$i++)
    <label class="mx-2">
      <input type="radio" value="{{ $i }}" name="lesson_week_count" class="icheck flat-green" required="true"
      @if($_edit===true && isset($item) && $item->has_tag("lesson_week_count", $i))
      checked
      @endif
      >{{$i}}回
    </label>
    @endfor
  </div>
</div>
