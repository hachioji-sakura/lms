<div class="col-12">
  <div class="form-group">
    <label for="teacher_character" class="w-100">
      {{__('messages.choose_grades')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    @foreach($grades as $grade)
      <label class="select-2">
        <input type="checkbox" value="{{ $grade->attribute_value }}" name="grade[]" class="icheck flat-green"
               @if(isset($textbook_grades) && in_array($grade->attribute_name,$textbook_grades,true))
               checked
          @endif>
        {{$grade->attribute_name}}
      </label>
    @endforeach

  </div>
</div>
