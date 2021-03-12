@if(isset($textbook))
  <div class="col-12 col-md-6">
    <div class="form-group">
      <label for="field1">
        テキスト名　
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" id="name" name="name" class="form-control" placeholder="例：サンプルテキスト"
             @if($_edit == true)
             value="{{$textbook->name}}"

             @endif
             required>
      @if($_edit == true)
      <input type="hidden" name="textbook" value="{{$textbook->id}}" alt="{{$textbook->name}}" />
      @endif
    </div>
  </div>
@endif
