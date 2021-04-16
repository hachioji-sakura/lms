<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="teacher_character" class="w-100">
      {{__('labels.grade')}}
      <span class="right badge badge-secondary">{{__('labels.optional')}}</span>
    </label>
    <select name="grade[]" class="w-100 form-control select2" width=100% multiple="multiple" >
      @foreach($grades as $grade)
        <option value="{{$grade->attribute_value}}"
          @if(isset($textbook_grades) && in_array($grade->attribute_name,$textbook_grades,true))
            selected
          @endif>
          {{$grade->attribute_name}}
        </option>
      @endforeach
    </select>
  </div>
</div>
