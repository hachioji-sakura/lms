@section('input_form')
<div class="col-12 mb-2" id="to_calendar_setting_form">
  <label for="start_date" class="w-100">
    {{__('labels.add_range')}}
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_to_calendar_date()">
      {{__('labels.calendar_button_this_month')}}
    </button>
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_to_calendar_date(1)">
      {{__('labels.calendar_button_next_month')}}
    </button>
  </label>
  <div class="input-group">
    <input type="text" name="start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="ex.2000/01/01"
    required="true"
    @if(isset($item) && isset($item['enable_start_date']) && $item['enable_start_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_start_date']))}}"
    @endif
    >
    <span class="float-left mx-2 mt-2">～</span>
    <input type="text" name="end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="ex.2000/01/01"
    required="true"
    greater="start_hours" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="trial_start_time1" not_equal_error="{{__('messages.validate_timezone_error')}}"
    @if(isset($item) && isset($item['enable_end_date']) && $item['enable_end_date']!='9999-12-31')
    value = "{{date('Y/m/d', strtotime($item['enable_end_date']))}}"
    @endif
    >
    <button type="button" class="btn btn-outline-success btn-sm ml-2" onClick="get_to_calendar_date()">
      <i class="fa fa-calendar-alt"></i>
      <span class="btn-label">{{__('labels.get')}}</span>
    </button>
  </div>
</div>
<div class="col-12 mb-2">
  <div>
  <table class="table table-striped" id="check_list">
    <thead>
    <tr class="bg-light">
      <th class="p-1 pl-2 text-sm">
        <label class="mx-2 mt-1" for="all_check_click">
          {{__('labels.schedule_add')}}{{__('labels.date')}}
        </label>
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
  var end = util.nowDate(0, next_month+1, -1);
  $("input[name='start_date']").val(start);
  $("input[name='end_date']").val(end);
  get_to_calendar_date();
}
function get_to_calendar_date(){
  var start_date = $("input[name='start_date']").val();
  var end_date = $("input[name='end_date']").val();
  var user_id = $("input[name='user_id']").val();
  start_date = start_date.replace_all('/', '-');
  end_date = end_date.replace_all('/', '-');
  var url = "/calendar_settings/to_calendar_data?start_date="+start_date+"&end_date="+end_date+"&user_id="+user_id;
  if($("input[name='id']").length > 0){
    var id = $("input[name='id']").val();
    url = "/calendar_settings/"+id+"/to_calendar_data?start_date="+start_date+"&end_date="+end_date;
  }
  $('button.btn-submit').attr('disabled', 'disabled');
  front.clearValidateError();
  if(!front.validateFormValue('to_calendar_setting_form')) return false;
  service.getAjax(false, url, {'loading': true},
    function(result, st, xhr) {
      var check_template = [
        '<tr class="">',
        '    <td class="p-1 text-sm text-center">',
        '    <div class="input-group">',
        '        <div class="form-check">',
        ' <small title="" class="badge badge-#style# mt-1 mr-1"><i class="fa fa-plus" ></i>#status_name#</small>',
        '        <label class="form-check-label">#date_label#',
        '        </label>',
        '        <input type="hidden" name="select_dates[]" value="#date#" > ',
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
        ' <small title="" class="badge badge-#style# mt-1 mr-1">登録済み</small>',
        '        </label>',
        '        <label class="form-check-label">#date_label#</label>',
        '        </div>',
        '    </div>',
        '    </td>',
        '</tr>'
      ].join('');

      $('#check_list tbody').empty();
      if(result['status']===200){
        console.log(result['data']);
        var is_find = false;
        var st;

        for(var date in result['data']){
          if(!util.isDate(date, '-')) continue;
          _template = check_template;
          if(!util.isEmpty(result['data'][date]['already_calendars']) && Object.keys(result['data'][date]['already_calendars']).length > 0){
            _template = already_template;
            for(var key in result['data'][date]['already_calendars']){
              st = status_style(result['data'][date]['already_calendars'][key]["status"]);
            }
          }
          else {
            st = {"name" : "新規登録", "style" : "info"};
            is_find = true;
          }
          var setting = result['data'][date]['setting'];
          var date_label = util.dateformat(date, '%Y年%m月%d(%w) ')+setting['from_time_slot'].substring(0,5)+'-'+setting['to_time_slot'].substring(0,5);
          date_label += '/'+setting['student_name'];
          var _dom = dom.textFormat(_template,
            {"date" : date,
             "date_label" : date_label,
             "status_name" : st["name"],
             "style" : st["style"]
           }
          );
          $('#check_list tbody').append(_dom);
        }

        if(is_find==true){
          $('button.btn-submit').removeAttr('disabled');
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
  if( $("input[name='select_dates[]']").length > 0){
    $("input[name='select_dates[]']").each(function(index, value){
      console.log(value);
      if(_is_scceuss===true) return ;
      var val = $(this).val();
      if(!util.isEmpty(val)) _is_scceuss = true;
    });
  }
  if(!_is_scceuss){
    front.showValidateError('#check_list', '登録可能な予定がありません。');
  }

  return _is_scceuss;
}
</script>
@endsection
@section('setting_form')
@if(isset($item))
<input type="hidden" name="id" value="{{$item->id}}">
<div id="accordion">
  <div class="row">
    <div class="col-12 p-2 pl-4">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="collapsed" aria-expanded="false">
        <i class="fa fa-chevron-down mr-1"></i>
        {{__('labels.regular_schedule_setting')}}
      </a>
    </div>
  </div>
  <div id="collapse1" class="panel-collapse collapse in">
    @component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
    @endcomponent
  </div>
</div>
@endif
@endsection
