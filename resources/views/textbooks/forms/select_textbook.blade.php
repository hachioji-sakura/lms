@if(isset($textbook))
  <div class="col-6">
    <div class="form-group">
      <label for="start_date" class="w-100">
        {{__('labels.textbooks')}}
      </label>
      <a alt="teacher_name" href="/teachers/{{$textbook->id}}" target="_blank">
        <i class="fas fa-book mr-1"></i>
        {{$textbook->name}}
      </a>
      <input type="hidden" name="textbook" value="{{$textbook->id}}" alt="{{$textbook->name}}" />
    </div>
  </div>
@endif
