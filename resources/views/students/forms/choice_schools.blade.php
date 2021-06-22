@for($i=1;$i<4;$i++)
<div class="col-12">
  <div class="col-12 col-md-6">
    <div class="form-group">
      <div class="input-group">
        <label for='choice_school1' class="w-100">
          {{__('labels.choice_school_'.$i)}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <select name="choice_school{{$i}}" id="choice_school{{$i}}" class="form-control select2" width="100%" onChange="choice_school_selectbox_change{{$i}}(this)">
          <option value="">{{__('labels.selectable')}}</option>
          <option value="other">その他の学校</option>
          @foreach($attributes['schools'] as $school)
          <option value="{{ $school->name }}">
            {{$school->name}}
          </option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-12 collapse choice_school_other_form{{$i}}">
  <div class="form-group">
    <label for="choice_school_other{{$i}}" class="w-100">
      第{{$i}}志望校を教えてください。
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" id="choice_school_other{{$i}}" name="choice_school_other{{$i}}" class="form-control" placeholder="例：八王子高等学校">
  </div>
</div>

<script>
  function choice_school_selectbox_change{{$i}}(obj){
    var choice_school =  $('#choice_school{{$i}}').val();

    if(choice_school === 'other'){
      $(".choice_school_confirm{{$i}}").collapse("hide");
      $(".choice_school_other_form{{$i}}").collapse("show");
      $(".choice_school_other_confirm{{$i}}").collapse("show");
    }
    else {
      $(".choice_school_confirm{{$i}}").collapse("show");
      $(".choice_school_other_form{{$i}}").collapse("hide");
      $(".choice_school_other_confirm{{$i}}").collapse("hide");
    }
  }
</script>

@endfor

