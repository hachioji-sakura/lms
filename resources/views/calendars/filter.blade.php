@if($is_list == true)
<div class="col-6 col-md-4">
  <div class="form-group">
    <label for="search_from_date" class="w-100">
      {{__('labels.date')}}(FROM)
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
      @if(isset($filter['calendar_filter']['search_from_date']))
        value="{{$filter['calendar_filter']['search_from_date']}}"
      @endif
      >
    </div>
  </div>
</div>
<div class="col-6 col-md-4">
  <div class="form-group">
    <label for="search_to_date" class="w-100">
      {{__('labels.date')}}(TO)
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
      </div>
      <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
      @if(isset($filter['calendar_filter']['search_to_date']))
        value="{{$filter['calendar_filter']['search_to_date']}}"
      @endif
      >
    </div>
  </div>
</div>
<div class="col-12 col-md-4">
  <div class="form-group">
    <label for="is_exchange" class="w-100">
      {{__('labels.sort_no')}}
    </label>
    <label class="mx-2">
    <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
    @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
      checked
    @endif
    >{{__('labels.date')}} {{__('labels.desc')}}
    </label>
  </div>
</div>
@endif
<div class="col-12 mb-2">
  <label for="search_work" class="w-100">
    {{__('labels.lesson_name')}}
  </label>
  <div class="w-100">
    @foreach($attributes['teaching_type'] as $index=>$name)
      <label class="mx-2">
      <input type="checkbox" value="{{$index}}" name="teaching_type[]" class="icheck flat-green"
        @if(isset($filter['calendar_filter']['teaching_type']) && in_array($index, $filter['calendar_filter']['teaching_type'])==true)
        checked
        @endif
        >{{$name}}
      </label>
    @endforeach
    <label class="mx-2">
    <input type="checkbox" value="1" name="is_exchange" class="icheck flat-green"
    @if(isset($filter['calendar_filter']['is_exchange']) && $filter['calendar_filter']['is_exchange']==true)
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
        @if(isset($filter['calendar_filter']['search_place']) && in_array($place->id, $filter['calendar_filter']['search_place'])==true)
        checked
        @endif
        >{{$place->name()}}
      </label>
    @endforeach
    <label class="mx-2">
      <input type="checkbox" value="true" name="search_is_online" class="icheck flat-green"
        @if(isset($filter['calendar_filter']['search_is_online']) && $filter['calendar_filter']['search_is_online']==true)
        checked
        @endif
        >{{__('labels.online')}}
    </label>
  </div>
</div>
<div class="col-4 mb-2">
  <label for="search_status" class="w-100">
    {{__('labels.status')}}
  </label>
  <div class="w-100">
    <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
      @foreach($attributes['calendar_status'] as $index => $name)
        @if($index=='lecture_cancel') @continue @endif
        @if($index=='rest') $index='rest,lecture_cancel' @endif
        <option value="{{$index}}"
        @if(isset($filter['calendar_filter']['search_status']) && in_array($index, $filter['calendar_filter']['search_status'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-4 mb-2">
  <label for="search_work" class="w-100">
    {{__('labels.body')}}
  </label>
  <div class="w-100">
    <select name="search_work[]" class="form-control select2" width=100% placeholder="作業" multiple="multiple" >
      @foreach($attributes['work'] as $index=>$name)
        <option value="{{$index}}"
        @if(isset($filter['calendar_filter']['search_work']) && in_array($index, $filter['calendar_filter']['search_work'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
    {{--
    @foreach($attributes['work'] as $index=>$name)
      <label class="mx-2">
      <input type="checkbox" value="{{$index}}" name="search_work[]" class="icheck flat-green"
        @if(isset($filter['calendar_filter']['search_work']) && in_array($index, $filter['calendar_filter']['search_work'])==true)
        checked
        @endif
        >{{$name}}
      </label>
    @endforeach
    --}}
  </div>
</div>
<div class="col-4 mb-2">
    <label for="search_word" class="w-100">
      {{__('labels.search_keyword')}}
    </label>
    <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
    @if(isset($filter['search_keyword']))
    value = "{{$filter['search_keyword']}}"
    @endif
    >
</div>
