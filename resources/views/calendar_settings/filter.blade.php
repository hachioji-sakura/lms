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
<div class="col-12 col-md-4">
  <label for="charge_subject" class="w-100">
    {{__('labels.week_day')}}
  </label>
  <div class="w-100">
    <select name="search_week[]" class="form-control select2" width=100%  multiple="multiple" >
      @foreach($attributes['lesson_week'] as $index=>$name)
        <option value="{{$index}}"
        @if(isset($filter['calendar_filter']['search_week']) && in_array($index, $filter['calendar_filter']['search_week'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 col-md-4">
  <label for="charge_subject" class="w-100">
    {{__('labels.work')}}
  </label>
  <div class="w-100">
    <select name="search_work[]" class="form-control select2" width=100%  multiple="multiple" >
      @foreach($attributes['work'] as $index=>$name)
        <option value="{{$index}}"
        @if(isset($filter['calendar_filter']['search_work']) && in_array($index, $filter['calendar_filter']['search_work'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 col-md-4">
  <label for="charge_subject" class="w-100">
    {{__('labels.place')}}
  </label>
  <div class="w-100">
    <select name="search_place[]" class="form-control select2" width=100% multiple="multiple" >
      @foreach($attributes['places'] as $place)
        <option value="{{$place->id}}"
        @if(isset($filter['calendar_filter']['search_place']) && in_array($place->id, $filter['calendar_filter']['search_place'])==true)
        selected
        @endif
        >{{$place->name()}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 mb-2">
  <label for="search_status" class="w-100">
    {{__('labels.status')}}
  </label>
  <div class="w-100">
    <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
      @foreach($attributes['setting_status'] as $index => $name)
        <option value="{{$index}}"
        @if(isset($filter['search_status']) && in_array($index, $filter['search_status'])==true)
        selected
        @endif
        >{{$name}}</option>
      @endforeach
    </select>
  </div>
</div>
