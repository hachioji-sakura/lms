@section('first_form')
  <div class="row">
    <input type="hidden" name="place_id" value="{{$place_id}}">
    <div class="col-12">
      <div class="form-group">
        <label for="textbooks" class="w-100">
          {{__('labels.place_textbooks')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.required')}}</span>
        </label>
        <select id="textbooks" name="textbooks[]" class="w-100 form-control select2" width="100%" multiple="multiple" >
          @foreach($textbooks as $textbook)
            <option value="{{$textbook->id}}"
              @if(isset($place_textbooks) && in_array($textbook->id,$place->textbooks->pluck('id')->toArray(),false))
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
