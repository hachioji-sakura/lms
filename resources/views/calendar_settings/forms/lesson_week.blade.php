<div class="col-6 mt-2">
  <div class="form-group">
    <label for="lesson_week" class="w-100">
      曜日
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    @foreach($attributes['lesson_week'] as $index => $name)
      @if(isset($calendar))
      {{-- この生徒（複数の場合は一人目）と、講師の曜日が有効な曜日を選択しに出す--}}
        @if($calendar['students'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===false && $calendar['teachers'][0]->user->has_tag('lesson_'.$index.'_time', 'disabled')===false)
        <label class="mx-2">
        <input type="radio" value="{{ $index }}" name="lesson_week" class="icheck flat-green" required="true"
          @if($item->lesson_week == $index)
          checked
          @endif
        >{{$name}}曜
        </label>
        @endif
      @endif
    @endforeach
  </div>
</div>
