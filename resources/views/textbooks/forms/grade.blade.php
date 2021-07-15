@empty(!$grades)
<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="{{$prefix}}grade" class="w-100">
      {{__('labels.grade')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <select id="{{$prefix}}grade" name="{{$prefix}}grade[]" class="w-100 form-control select2" width=100% multiple="multiple" >
      @foreach($grades as $grade)
        <option value="{{$grade->attribute_value}}"
          @if(isset(request()->search_grade) && in_array($grade->attribute_value, request()->search_grade,false))
          selected
          @endif
          @if(isset($textbook_grades) && in_array($grade->attribute_name,$textbook_grades,true))
          selected
          @endif
        >
          {{$grade->attribute_name}}
        </option>
      @endforeach
    </select>
  </div>
</div>
@endempty
