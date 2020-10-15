<div class="col-12">
  <div class="form-group">
    <label for="season_school_lesson_milestone" class="w-100">
      講習にてご希望される授業内容をお知らせください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($attributes['season_school_lesson_milestone'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="season_school_lesson_milestone[]" class="icheck flat-green"  onChange="season_school_lesson_milestone_checkbox_change(this)"
      @if($_edit==true && $item->has_tag('season_school_lesson_milestone', $index)==true) checked @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
<div class="col-12 collapse season_school_lesson_milestone_word_form">
  <div class="form-group">
    <label for="season_school_lesson_milestone_word" class="w-100">
      その他を選択した場合について、内容をご記入ください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" id="season_school_lesson_milestone_word" name="season_school_lesson_milestone_word" class="form-control" placeholder="例：学習習慣を身に着けたい" >
  </div>
</div>
