<div class="col-12">
  <div class="form-group">
    <label for="title" class="w-100">
      {{__('labels.students')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <select name="student_id" class="form-control select2" width=100% placeholder="{{__('labels.charge_student')}}" required="true" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($charge_students as $item)
        <option value="{{$item->id}}"
          grade="{{$item->user->details('students')->tag_value('grade')}}"
          >{{$item->name()}}</option>
      @endforeach
    </select>
  </div>
</div>

<script>
$(function(){
});
</script>
