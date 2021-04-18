<div class="col-12">
  <div class="form-group">
    <label for="teacher_character" class="w-100">
      {{__('labels.subject')}}
      <span class="right badge badge-secondary">{{__('labels.optional')}}</span>
    </label>
    <select name="subject[]" class="form-control select2 w-100" width="100%" multiple="multiple" >
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
