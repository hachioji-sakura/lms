<div class="form-group">
  <label for="{{$prefix}}gender">
    {{__('labels.gender')}}
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <div class="form-check">
        <input class="form-check-input icheck flat-green" type="radio" name="{{$prefix}}gender" id="{{$prefix}}gender_2" value="2" required="true"
        @if(isset($item) && isset($item->gender) && $item->gender===2)
          checked
        @endif
        >
        <label class="form-check-label" for="{{$prefix}}gender_2">
            {{__('labels.woman')}}
        </label>
    </div>
    <div class="form-check ml-2">
        <input class="form-check-input icheck flat-green" type="radio" name="{{$prefix}}gender" id="{{$prefix}}gender_1" value="1" required="true"
        @if(isset($item) && isset($item->gender) && $item->gender===1)
         checked
        @endif
        >
        <label class="form-check-label" for="{{$prefix}}gender_1">
            {{__('labels.man')}}
        </label>
    </div>
  </div>
</div>
