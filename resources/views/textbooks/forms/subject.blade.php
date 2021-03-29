<div class="col-12">
  <div class="form-group">
    <label for="subject" class="w-100">
      {{__('messages.choose_subjects')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($subjects as $subject)
    <label class="select2">
    <input type="checkbox" value="{{ $subject->id }}" name="subject[]" class="icheck flat-green"
     @if(isset($textbook_subjects) && in_array($subject->name,$textbook_subjects,true))
     checked
     @endif>
      {{$subject->name}}
    </label>
    @endforeach
  </div>
</div>