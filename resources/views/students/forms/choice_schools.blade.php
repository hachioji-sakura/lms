@for($i=1;$i<4;$i++)
<div class="col-12 col-md-6 subject_form">
  <div class="form-group">
    <div class="input-group">
      <label for='choice_school1' class="w-100">
        {{__('labels.choice_school_'.$i)}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <select name="choice_school{{$i}}" class="form-control select2" width="100%">
        <option value="">{{__('labels.selectable')}}</option>
        @foreach($attributes['schools'] as $school)
          <option value="{{ $school->name }}">
            {{$school->name}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
@endfor
