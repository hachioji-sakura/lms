<div class="col-12">
  <div class="form-group">
    <label for="subject" class="w-100">
      テキストに当てはまる学年を選択してください
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($subjects as $subject)
    <label class="mx-2">
    <input type="checkbox" value="{{ $subject->id }}" name="subject[]" class="icheck flat-green"
     @if(isset($subjects) && in_array($subject->name,$textbookSubjects,true))
     checked
     @endif>
      {{$subject->name}}
    </label>
    @endforeach
  </div>
</div>
