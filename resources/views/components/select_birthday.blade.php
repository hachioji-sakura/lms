<div class="form-group">
  <label for="birth_day" class="w-100">
    生年月日
    <span class="right badge badge-danger ml-1">必須</span>
  </label>
  <select name="birth_day_year" class="form-control w-25 float-left mr-1" width="25%"  accessKey="year" placeholder="生年月日(年)" defaultSelect="@isset($item) {{date('Y', strtotime($item->birth_day))}} @endisset" required="true">
  </select>
  <select name="birth_day_month" class="form-control w-20 float-left mr-1" width="20%"  accessKey="month" placeholder="生年月日(月)" defaultSelect="@isset($item) {{intval(date('m', strtotime($item->birth_day)))}} @endisset" required="true">
  </select>
  <select name="birth_day_day" class="form-control w-20 float-left" width="20%" accessKey="day" placeholder="生年月日(日)" defaultSelect="@isset($item) {{intval(date('d', strtotime($item->birth_day)))}} @endisset" required="true">
  </select>
  <input id="birth_day" type="hidden" class="form-control" name="birth_day"  inputtype="date" placeholder="例：2000/01/01" required="true" value="@isset($item) {{$item->birth_day}} @endisset">
</div>
<script>
$(function(){
  $('select[name="birth_day_year"],select[name="birth_day_month"],select[name="birth_day_day"]').on('change', function(e){
    var year = $('select[name="birth_day_year"]').val();
    var month = $('select[name="birth_day_month"]').val();
    var day = $('select[name="birth_day_day"]').val();
    var date = year+'/'+month+'/'+day;
    $('input[name="birth_day"]').val(date);
  });
});
</script>
