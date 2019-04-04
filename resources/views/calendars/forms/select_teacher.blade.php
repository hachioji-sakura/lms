@if(count($items) > 1)
<div class="col-12">
  <div class="form-group">
    <label for="title" class="w-100">
      講師
      <span class="right badge badge-danger ml-1">必須</span>
    </label>

    <select name="teacher_id" class="form-control" placeholder="担当講師" required="true">
      @foreach($items as $teacher)
         <option value="{{ $teacher->id }}" @if(isset($_edit) && $item['teacher_id'] == $teacher->id) selected @endif>{{$teacher->name()}}</option>
      @endforeach
    </select>
  </div>
</div>
@else
  @isset($item['teacher_id'])
  <input type="hidden" name="teacher_id" value="{{$item['teacher_id']}}" />
  @endisset
@endif
