    <label>{{__('labels.curriculums_name')}}</label>
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    <select name="curriculum_ids[]" class="form-control select2" id="curriculum_select" width=100%  multiple="multiple">
      @foreach($curriculums as $curriculum)
      <option value="{{$curriculum->id}}"
      @if(!empty($item) && $_edit)
        {{$item->curriculums->contains($curriculum->id)  ? "selected" : "" }}
      @endif
      >{{$curriculum->name}}</option>
      @endforeach
    </select>
