<div class="col-12 mb-2">
  <div class="form-group">
    <label for="lesson_name" class="w-100">
      {{__('labels.lesson_name')}}
    </label>
    {{--
    <label class="mx-2">
    <input type="checkbox" value="add" name="teaching_type[]" class="icheck flat-green"
    @if(isset($filter['add_lesson']) && $filter['add_lesson']==true)
      checked
    @endif
    >{{__('labels.add')}}{{__('labels.school_lesson')}}
    </label>
    --}}
    <label class="mx-2">
    <input type="checkbox" value="exchange" name="teaching_type[]" class="icheck flat-green"
    @if(isset($filter['exchange_lesson']) && $filter['exchange_lesson']==true)
      checked
    @endif
    >{{__('labels.exchange')}}{{__('labels.school_lesson')}}
    </label>
    <label class="mx-2">
    <input type="checkbox" value="trial" name="teaching_type[]" class="icheck flat-green"
    @if(isset($filter['trial_lesson']) && $filter['trial_lesson']==true)
      checked
    @endif
    >{{__('labels.trial')}}{{__('labels.school_lesson')}}
    </label>
    <label class="mx-2">
    <input type="checkbox" value="1" name="is_exchange" class="icheck flat-green"
    @if(isset($filter['is_exchange']) && $filter['is_exchange']==true)
      checked
    @endif
    >{{__('labels.to_exchange')}}
    </label>
  </div>
</div>
<div class="col-12 mb-2">
  <label for="search_place" class="w-100">
    {{__('labels.place')}}
  </label>
  <div class="w-100">

    @foreach($attributes['places'] as $place)
      <label class="mx-2">
      <input type="checkbox" value="{{$place->id}}" name="search_place[]" class="icheck flat-green"
        @if(isset($filter['search_place']) && in_array($place->id, $filter['search_place'])==true)
        checked
        @endif
        >{{$place->name()}}
      </label>
    @endforeach
  </div>
</div>
<div class="col-12 mb-2">
  <label for="search_work" class="w-100">
    {{__('labels.body')}}
  </label>
  <div class="w-100">
    @foreach($attributes['work'] as $index=>$name)
      <label class="mx-2">
      <input type="checkbox" value="{{$index}}" name="search_work[]" class="icheck flat-green"
        @if(isset($filter['search_work']) && in_array($index, $filter['search_work'])==true)
        checked
        @endif
        >{{$name}}
      </label>
    @endforeach
  </div>
</div>
<div class="col-12 mb-2">
  <label for="search_status" class="w-100">
    {{__('labels.status')}}
  </label>
  <div class="w-100">
    <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
      @foreach(config('attribute.calendar_status') as $index => $name)
        @if($index=='lecture_cancel') @continue @endif
        @if($index=='rest') $index='rest,lecture_cancel' @endif
        <option value="{{$index}}"
        @if(isset($filter['search_status']) && in_array($index, $filter['search_status'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
