<div class="col-12 col-md-6">
  <div class="form-group">
    <label for="field1">
      {{__('labels.textbook_name')}}
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    </label>
    <input type="text" id="name" name="name" class="form-control" placeholder="例：１、２年の総合復習１"
     @if(isset($textbook))
     value="{{$textbook->name}}"
     @endif
     required>
    @if(isset($textbook))
    <input type="hidden" name="textbook" value="{{$textbook->id}}" alt="{{$textbook->name}}" />
    @endif
  </div>
</div>

