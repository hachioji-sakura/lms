@section('setting_form')
<div id="accordion">
  <div class="row">
    <div class="col-12 p-2 pl-4">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="collapsed" aria-expanded="false">
        <i class="fa fa-chevron-down mr-1"></i>
        繰り返しスケジュール設定
      </a>
    </div>
  </div>
  <div id="collapse1" class="panel-collapse collapse in">
    @component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
    @endcomponent
  </div>
</div>
@endsection
@section('input_form')
<div class="col-12 mb-2" id="to_calendar_setting_form">
  <input type="hidden" name="id" value="{{$item->id}}">
  <label for="start_date" class="w-100">
    登録範囲
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    required="true"
    @if(isset($item) && isset($item['enable_start_date']) && $item['enable_start_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_start_date']))}}"
    @endif
    >
    <span class="float-left mx-2 mt-2">～</span>
    <input type="text" name="end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    required="true"
    greater="start_hours" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time1" not_equal_error="{{__('messages.validate_timezone_error')}}"
    @if(isset($item) && isset($item['enable_end_date']) && $item['enable_end_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_end_date']))}}"
    @endif
    >
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_to_calendar_date()">
      当月
    </button>
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_to_calendar_date(1)">
      次月
    </button>
    <button type="button" class="btn btn-outline-success btn-sm ml-2" onClick="get_to_calendar_date()">
      <i class="fa fa-calendar-alt mr-1"></i>
      登録日取得
    </button>
  </div>
</div>
<div class="col-12 mb-2">
  <div>
  <table class="table table-striped" id="check_list">
    <thead>
    <tr class="bg-light">
      <th class="p-1 pl-2 text-sm">
        <i class="fa fa-plus mt-1"></i>
        <label class="mx-2 mt-1" for="all_check_click">
          {{__('labels.add')}}
        </label>
        <input class="form-check-input icheck flat-green ml-2" type="checkbox" name="all_check_click" value="delete" onChange='dom.allChecked(this, "check_list");' >
      </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
  </div>
</div>
<script>
function set_to_calendar_date(next_month){
  if(!next_month) next_month = 0;
  var start = util.nowDate(0, next_month, 0);
  var end = util.nowDate(0, next_month+1, 0);
  $("input[name='start_date']").val(start);
  $("input[name='end_date']").val(end);
}
function get_to_calendar_date(){
  var start_date = $("input[name='start_date']").val();
  var end_date = $("input[name='end_date']").val();
  start_date = start_date.replace_all('/', '-');
  end_date = end_date.replace_all('/', '-');
  var id = $("input[name='id']").val();
  var url = "/calendar_settings/"+id+"/to_calendar_data?start_date="+start_date+"&end_date="+end_date;
  $('button.btn-submit').collapse('hide');
  front.clearValidateError();
  if(!front.validateFormValue('to_calendar_setting_form')) return false;
  service.getAjax(false, url, null,
    function(result, st, xhr) {
      var check_template = [
              '<tr class="">',
      				'    <td class="p-1 text-sm text-center">',
      				'    <div class="input-group">',
      				'        <div class="form-check">',
      				'        <input class="form-check-input icheck flat-green calendar_member_delete" type="checkbox" name="select_dates[]" id="date_check_#date#" value="#date#" ',
              ' checked>',
      				'        <label class="form-check-label" for="date_check_#date#">#date_lavel#</label>',
      				'        </div>',
      				'    </div>',
      				'    </td>',
      				'</tr>'
      ].join('');
      var already_template = [
              '<tr class="">',
      				'    <td class="p-1 text-sm text-center">',
      				'    <div class="input-group">',
      				'        <div class="form-check">',
      				'        <i class="fa fa-calendar" ></i>',
      				'        <label class="form-check-label">#date_lavel# 登録済み</label>',
      				'        </div>',
      				'    </div>',
      				'    </td>',
      				'</tr>'
      ].join('');

      $('#check_list tbody').empty();
      if(result['status']===200){
        console.log(result['data']);
        var is_find = false;
        for(var date in result['data']){
          if(!util.isDate(date, '-')) continue;
          _template = check_template;
          if(!util.isEmpty(result['data'][date]) && Object.keys(result['data'][date]).length > 0){
            _template = already_template;
          }
          else {
            is_find = true;
          }
          var date_lavel = util.dateformat(date, '%Y年%m月%d');
          var _dom = dom.textFormat(_template, {"date" : date, "date_lavel" : date_lavel});
          $('#check_list tbody').append(_dom);
        }

        if(is_find==true){
          $('button.btn-submit').collapse('show');
        }
        else {
          front.showValidateError('#check_list', '登録可能な日付がありません');
        }
      }
      else {
        front.showValidateError('#check_list', '登録可能な日付がありません');
      }
      base.pageSettinged('check_list', null);
    },
    function(xhr, st, err) {
        messageCode = "error";
        messageParam= "\n"+err.message+"\n"+xhr.responseText;
        alert("カレンダー取得エラー\n画面を再表示してください\n"+messageParam);
    }
  ,true);

}
function select_dates_check_validate(){
  var _is_scceuss = false;
  if( $("input[type='checkbox'][name='select_dates[]']").length > 0){
    $("input[type='checkbox'][name='select_dates[]']:checked").each(function(index, value){
      if(_is_scceuss===true) return ;
      var val = $(this).val();
      if(!util.isEmpty(val)) _is_scceuss = true;
    });
  }
  if(!_is_scceuss){
    front.showValidateError('#check_list', '登録日を１つ以上選択してください');
  }

  return _is_scceuss;
}
</script>
@endsection
@section('third_form')
@endsection
@section('confirm_form')
@endsection
