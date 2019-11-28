@if(isset($teachers) && count($teachers)==1)
<div class="col-6">
  <div class="form-group">
    <label for="start_date" class="w-100">
      {{__('labels.teachers')}}
    </label>
    <a alt="teacher_name" href="/teachers/{{$teachers[0]->id}}" target="_blank">
    <i class="fa fa-user-tie mr-1"></i>
    {{$teachers[0]->name()}}
    </a>
    <input type="hidden" name="teacher_id" value="{{$teachers[0]->id}}" alt="{{$teachers[0]->name()}}" />
  </div>
</div>
@endif
