<div class="col-12">
  <div class="form-group">
    <label for="howto" class="w-100">
      この講師に当てはまる属性を選択してください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($attributes['teacher_character'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="teacher_character[]" class="icheck flat-green"
      @if(isset($item) && $item->user->has_tag('teacher_character', $index)===true)
      checked
      @endif
       >{{$name}}
    </label>
    @endforeach
  </div>
</div>
