<div class="col-12">
  <div class="form-group">
    <label for="student_type" class="w-100">
      この生徒の属性を選択してください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($attributes['student_type'] as $index => $name)
    <label class="mx-2">
      <input type="checkbox" value="{{ $index }}" name="student_type[]" class="icheck flat-green"
      @if(isset($item) && $item->user->has_tag('student_type', $index)===true)
      checked
      @endif
       >{{$name}}
    </label>
    @endforeach
  </div>
</div>
