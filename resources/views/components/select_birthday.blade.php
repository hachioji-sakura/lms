<div class="form-group">
  <label for="birth_day" class="w-100">
    {{__('labels.birth_day')}}
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <select name="birth_day_year" class="form-control w-25 float-left mr-1"  placeholder="生年月日(年)"  required="true" onChange="birth_day_form_change()">
    @for($i=date('Y');$i>=(intval(date('Y'))-100);$i--)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && $i == intval(date('Y', strtotime($item->birth_day))))
        checked
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <span class="float-left mt-2 mx-2">/</span>
  <select name="birth_day_month" class="form-control w-20 float-left mr-1"  placeholder="生年月日(月)" required="true" onChange="birth_day_form_change()">
    <span class="float-left">/</span>
    @for($i=1;$i<13;$i++)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && $i == intval(date('m', strtotime($item->birth_day))))
        checked
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <span class="float-left mt-2 mx-2">/</span>
  <select name="birth_day_day" class="form-control w-20 float-left"  placeholder="生年月日(日)"  required="true" onChange="birth_day_form_change()">
    @for($i=1;$i<32;$i++)
    <option value="{{$i}}"
      @if(isset($_edit) && $_edit==true && $i == intval(date('d', strtotime($item->birth_day))))
        checked
      @endif
      >{{$i}}</option>
    @endfor
  </select>
  <input id="birth_day" type="hidden" class="form-control" name="birth_day"  inputtype="date" placeholder="例：2000/01/01" required="true" value="@isset($item->birth_day){{$item->birth_day}}@endisset">
</div>
<script>
function birth_day_form_change(){
    var year = $('select[name="birth_day_year"]').val();
    var month = $('select[name="birth_day_month"]').val();
    var day = $('select[name="birth_day_day"]').val();
    var date = year+'/'+month+'/'+day;
    console.log(date);
    $('input[name="birth_day"]').val(date);
}
</script>
