<div class="form-group">
  <label for="birth_day" class="w-100">
    {{__('labels.birth_day')}}
    @if(!(isset($is_label) && $is_label==true))
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    @endif
  </label>
  @if(isset($is_label) && $is_label==true)
  <span>
    @if(!empty($item->birth_day))
    {{__('labels.year_month_day', ['year' => date('Y', strtotime($item->birth_day)), 'month' => date('m', strtotime($item->birth_day)), 'day' => date('d', strtotime($item->birth_day))])}}
    @endif
  </span>
  @else
  <select name="{{$prefix}}birth_day_year" class="form-control w-25 float-left mr-1"  placeholder="生年月日(年)"  required="true" onChange="birth_day_form_change()">
    <option value="">({{__('labels.select')}})</option>
    @for($i=date('Y');$i>=(intval(date('Y'))-100);$i--)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && !empty($item->birth_day) && $i == intval(date('Y', strtotime($item->birth_day))))
        selected
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <span class="float-left mt-2 mx-2">
    {{__('labels.sep_year')}}
  </span>
  <select name="{{$prefix}}birth_day_month" class="form-control w-20 float-left mr-1"  placeholder="生年月日(月)" required="true" onChange="birth_day_form_change()">
    <option value="">({{__('labels.select')}})</option>
    @for($i=1;$i<13;$i++)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && !empty($item->birth_day) && $i == intval(date('m', strtotime($item->birth_day))))
      selected
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <span class="float-left mt-2 mx-2">
    {{__('labels.sep_month')}}
  </span>
  <select name="{{$prefix}}birth_day_day" class="form-control w-20 float-left"  placeholder="生年月日(日)"  required="true" onChange="birth_day_form_change()">
    <option value="">({{__('labels.select')}})</option>
    @for($i=1;$i<32;$i++)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && !empty($item->birth_day) && $i == intval(date('d', strtotime($item->birth_day))))
      selected
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <span class="float-left mt-2 mx-2">
    {{__('labels.sep_day')}}
  </span>
  @endif
  <input id="{{$prefix}}birth_day" type="hidden" class="form-control birth_day" name="{{$prefix}}birth_day"  inputtype="date" placeholder="例：2000/01/01" required="true"
    value="@isset($item->birth_day){{$item->birth_day}}@endisset">
</div>
