<div class="col-12">
  <div class="form-group">
    <label for="email" class="w-100">
      {{__('labels.email')}}
      @if(!(isset($is_label) && $is_label===true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label===true)
    <span>{{$item['email']}}</span>
    @else
    <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email"
    @if(isset($_edit) && $_edit==true)
    value = '{{$item->email}}'
    @endif
    >
    @endif
  </div>
</div>
