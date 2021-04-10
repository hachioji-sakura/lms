<div class="col-12">
  <div class="form-group">
    <label for="teacher_character" class="w-100">
      {{__('messages.choose_subjects')}}
      <span class="right badge badge-secondary">{{__('labels.optional')}}</span>
    </label>
    <div class="col-6">
      <select name="subject[]" class="w-100 form-control select2" width=100% multiple="multiple" >
        @foreach($subjects as $subject)
          <option value="{{$subject->id}}"
            @if(isset($textbook_subjects) && in_array($subject->name,$textbook_subjects,true))
             selected
            @endif>
            {{$subject->name}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>



