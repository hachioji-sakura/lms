<div class="col-12">
  <div class="form-group">
    <label for="howto" class="w-100">
      この生徒に当てはまる属性を選択してください
      <span class="right badge badge-secondary ml-1">任意</span>
    </label>
    @foreach($attributes['student_character'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="student_character[]" class="icheck flat-green"
      @if(isset($item) && $item->user->has_tag('student_character', $index)===true)
      checked
      @endif
      >{{$name}}
    </label>
    @endforeach
  </div>
</div>
