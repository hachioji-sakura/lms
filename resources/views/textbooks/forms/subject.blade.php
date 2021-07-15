@empty(!$subjects)
<div class="col-12">
  <div class="form-group">
    <label for="{{$prefix}}subject" class="w-100">
      {{__('labels.subjects')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <select id="{{$prefix}}subject" name="{{$prefix}}subject[]" class="w-100 form-control select2" width="100%" multiple="multiple" >
      @foreach($subjects as $subject)
        <option value="{{$subject->id}}"
          @if(isset(request()->search_subject) && in_array($subject->id, request()->search_subject,false))
          selected
          @endif
          @if(isset($textbook_subjects) && in_array($subject->name,$textbook_subjects,true))
          selected
          @endif
        >
          {{$subject->name}}
        </option>
      @endforeach
    </select>
  </div>
</div>
@endempty
