<div class="col-12">
  <div class="form-group">
    <label for="teacher_character" class="w-100">
      {{__('labels.choose_grades')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>

    @foreach($grades as $grade)
      <label class="mx-2">
        <input type="checkbox" value="{{ $grade->id }}" name="grade_no[]" class="icheck flat-green"
               @if(isset($textbookGrades) && in_array($grade->attribute_name,$textbookGrades,true))
               checked
          @endif>
        {{$grade->attribute_name}}
      </label>
    @endforeach

  </div>
</div>
