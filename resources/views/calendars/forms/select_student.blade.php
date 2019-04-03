@if(count($items) > 0)
<div class="col-12">
  <div class="form-group">
    <label for="title" class="w-100">
      生徒
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <select name="student_id" class="form-control select2" width=100% placeholder="担当生徒" required="true">
      <option value="">(選択)</option>
      @foreach($items as $student)
         <option value="{{ $student->id }}" @if(isset($_edit) && $item['student_id'] == $student->id) selected @endif>{{$student->name()}}</option>
      @endforeach
    </select>
  </div>
</div>
@endif
