<div class="col-12 col-md-6 subject_form">
  <div class="form-group">
    <div class="input-group">
      <label for='choice_school1' class="w-100">
        {{__('labels.first_choice_school')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <select name="choice_school1" class="form-control select2" width="100%">
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

<div class="col-12 col-md-6 subject_form">
  <div class="form-group">
    <div class="input-group">
      <label for='choice_school2' class="w-100">
        {{__('labels.second_choice_school')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <select name="choice_school2" class="form-control select2" width="100%">
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

<div class="col-12 col-md-6 subject_form">
  <div class="form-group">
    <div class="input-group">
      <label for='choice_school3' class="w-100">
        {{__('labels.third_choice_school')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <select name="choice_school3" class="form-control select2" width="100%">
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

