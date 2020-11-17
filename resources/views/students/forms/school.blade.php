<div class="col-12
@if(isset($is_label) && $is_label==true)
col-md-6
@else
col-md-3
@endif
 ">
  <div class="form-group">
    <label for="{{$prefix}}grade" class="w-100">
      {{__('labels.grade')}}
      @if(!(isset($is_label) && $is_label==true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label==true)
      @if(isset($item) && !empty($item->grade()))
      <span>
        {{$item->grade()}}
      </span>
      <input type="hidden" name="{{$prefix}}grade_name" value="{{$item->grade()}}">
      <input type="hidden" class="grade" name="{{$prefix}}grade" value="{{$item->get_tag_value('grade')}}">
      @endif
    @else
    <select name="{{$prefix}}grade" class="form-control grade" placeholder="学年" required="true" onChange="subject_onload()" accesskey="{{$prefix}}grade" >
      <option value="">{{__('labels.selectable')}}</option>
      @foreach($attributes['grade'] as $index => $name)
        <option value="{{$index}}"
        @if(isset($_edit) && $_edit==true && isset($item) && !empty($item) && $index==$item->get_tag_value('grade')) selected @endif
        >{{$name}}</option>
      @endforeach
    </select>
    @endif
  </div>
</div>
<div class="col-12
@if(isset($is_label) && $is_label==true)
col-md-6
@else
col-md-9
@endif
{{$prefix}}grade_school_name_form">
  <div class="form-group">
    <label for="{{$prefix}}school_name" class="w-100">
      {{__('labels.school_name')}}
      @if(!(isset($is_label) && $is_label==true))
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @endif
    </label>
    @if(isset($is_label) && $is_label==true)
      @if(isset($item) && !empty($item->school_name()))
      <span>
        {{$item->school_name()}}
      </span>
      @endif
    @else
    <input type="text" id="{{$prefix}}school_name" name="{{$prefix}}school_name" class="form-control" required="true" placeholder="例：八王子市立サクラ中学校"
      @if(isset($_edit) && $_edit==true && isset($item) && !empty($item->get_tag_value('school_name'))) value="{{$item->get_tag_value('school_name')}}" @endif
      >
    @endif
  </div>
</div>
<script>
$(function(){
  subject_onload();
});
</script>
