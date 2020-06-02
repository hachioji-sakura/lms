<label>{{__('labels.subject')}}</label>
<span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
<select name="subject_ids[]" class="form-control select2" width="100%" multiple="multiple" required="true">
  @foreach($subjects as $subject)
  <option value="{{$subject->id}}"
  @if(!empty($item) && $_edit)
    {{$item->subjects->contains($subject->id)  ? "selected" : "" }}
  @endif
  >{{$subject->name}}</option>
  @endforeach
</select>
