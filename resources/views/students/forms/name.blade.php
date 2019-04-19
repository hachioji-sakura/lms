<div class="col-6 col-lg-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="{{$prefix}}name_last" class="w-100">
      氏
    </label>
    <span id="student_name_last">{{$item['name_last']}}</span>
    @else
    <label for="{{$prefix}}name_last">
      氏
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="name_last" name="{{$prefix}}name_last" class="form-control" placeholder="例：八王子" required="true" inputtype="zenkaku" @isset($item) value="{{$item['name_last']}}" @endisset>
    @endif
  </div>
</div>
<div class="col-6 col-lg-6 col-md-6">
  <div class="form-group">
    @if(isset($is_label) && $is_label===true)
    <label for="student_name_first" class="w-100">
      名
    </label>
    <span id="student_name_first">{{$item['name_first']}}</span>
    @else
    <label for="{{$prefix}}name_first">
      名
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <input type="text" id="name_first" name="{{$prefix}}name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku" @isset($item) value="{{$item['name_first']}}" @endisset>
    @endif
  </div>
</div>
