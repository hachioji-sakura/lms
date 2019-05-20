@if(isset($teachers))
  @if(count($teachers)==1)
  <div class="col-12">
    <div class="form-group">
      <label for="start_date" class="w-100">
        講師
      </label>
      <a href="/teachers/{{$teachers[0]->id}}" target="_blank">
      <i class="fa fa-user-tie mr-1"></i>
      {{$teachers[0]->name()}}
      </a>
      <input type="hidden" name="teacher_id" value="{{$teachers[0]->id}}" />
    </div>
  </div>
  @endif
@endif
