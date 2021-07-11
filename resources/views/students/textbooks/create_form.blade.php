@section('first_form')
  <div class="row">
    <input type="hidden" name="student_id" value="{{$student_id}}">
    <div class="col-12">
      <div class="form-group">
        <label for="textbooks" class="w-100">
          {{__('labels.student_textbooks')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.required')}}</span>
        </label>
        <select id="textbooks" name="textbooks[]" class="w-100 form-control select2" width="100%" multiple="multiple" >
          @foreach($textbooks as $textbook)
          <option value="{{$textbook->id}}"
            @if(isset($student_textbooks) && in_array($textbook->id,$student->textbooks->pluck('id')->toArray(),false))
            selected
            @endif
          >
            {{$textbook->name}}
          </option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
@endsection
