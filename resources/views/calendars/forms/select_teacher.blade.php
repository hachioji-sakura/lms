@if(isset($teacher))
  <div class="col-12">
    <div class="form-group">
      <label for="start_date" class="w-100">
        講師
      </label>
      <i class="fa fa-user-tie mr-1"></i>
      {{$teacher->name()}}
      <input type="hidden" name="teacher_id" value="{{$teacher->id}}" />
    </div>
  </div>
@endif
