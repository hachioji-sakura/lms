<div class="col-12">
  <div class="form-group">
    <label for="email" class="w-100">
      ログインID
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <span>{{$item['email']}}</span>
    @else
    <input type="text" id="student_id" name="email" class="form-control" placeholder="任意の英数字" required="true" inputtype="alnum">
    @endif
  </div>
</div>
