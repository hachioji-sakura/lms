@section('setting_form')
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
@endsection
@section('input_form')
<div class="col-12 mb-2" id="delete_calendar_setting_form">
  <input type="hidden" name="id" value="{{$item->id}}">
  <input type="hidden" name="setting_user_id" value="{{$item->user_id}}">
  <label for="start_date" class="w-100">
    {{__('labels.add_range')}}
    <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_delete_calendar_date()">
      {{__('labels.calendar_button_this_month')}}
    </button>
    <button type="button" class="btn btn-default btn-sm ml-2" onClick="set_delete_calendar_date(1)">
      {{__('labels.calendar_button_next_month')}}
    </button>
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
    <button type="button" class="btn btn-outline-success btn-sm ml-2" onClick="get_delete_calendar_date()">
      <i class="fa fa-calendar-alt mr-1"></i>
      <span class="btn-label">{{__('labels.get')}}</span>
    </button>
  </div>
</div>
<div class="col-12 mb-2">
  <div>
  <table class="table" id="check_list">
    <thead>
    <tr class="bg-light">
      <th class="p-1 pl-2 text-sm">
        <i class="fa fa-plus mt-1"></i>
        <label class="mx-2 mt-1" for="all_check_click">
          {{__('labels.schedule_delete')}}
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
function set_delete_calendar_date(next_month){
  if(!next_month) next_month = 0;
  var start = util.nowDate(0, next_month, 0);
  var end = util.nowDate(0, next_month+1, -1);
  $("input[name='start_date']").val(start);
  $("input[name='end_date']").val(end);
  get_delete_calendar_date();
}
function get_delete_calendar_date(){
  var start_date = $("input[name='start_date']").val();
  var end_date = $("input[name='end_date']").val();
  start_date = start_date.replace_all('/', '-');
  end_date = end_date.replace_all('/', '-');
  var id = $("input[name='id']").val();
  var setting_user_id = $("input[name='setting_user_id']").val();
  var url = "/api_calendars/"+setting_user_id;
  var req = {
    "from_date" : start_date,
    "to_date" : end_date,
    "user_calendar_setting_id" : id,
    "loading" : true,
  };
  $('button.btn-submit').collapse('hide');
  if(!front.validateFormValue('delete_calendar_setting_form')) return false;
  front.clearValidateError();
  service.getAjax(false, url, req,
    function(result, st, xhr) {
      var check_template = [
              '<tr class="">',
      				'    <td class="p-1 text-sm text-center">',
      				'    <div class="input-group">',
      				'        <div class="form-check">',
      				'        <input class="form-check-input icheck flat-red calendar_member_delete" type="checkbox" name="select_ids[]" id="date_check_#date#" value="#id#" ',
              ' checked>',
      				'        <label class="form-check-label" for="date_check_#date#">',
              '           #date#(ID=#id#)',
              ' <small title="{{$item["id"]}}" class="badge badge-#style# mt-1 mr-1">#status#</small>',
              '        </label>',
      				'        </div>',
      				'    </div>',
      				'    </td>',
      				'</tr>'
      ].join('');
      $('#check_list tbody').empty();
      if(result['status']===200){
        console.log(result['data']);
        var is_find = false;
        for(var i=0,n=result['data'].length;i<n;i++){
          var item  = result['data'][i];
          console.log(item);
          _template = check_template;
          date = item['start_time'];
          if(date.length > 8) date = date.substring(0,10);
          date = date.replace_all('-', '');
          date = util.dateformat(date, '%Y年%m月%d');
          style = status_style(item["status"]);
          var _dom = dom.textFormat(_template, {"date" : date, "id" : item["id"], "status" : item["status_name"], "style" : style["style"]});
          $('#check_list tbody').append(_dom);
          is_find = true;
        }
      }
      if(is_find==true){
        $('button.btn-submit').collapse('show');
      }
      else {
        front.showValidateError('#check_list', '予定を取得できません');
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
function select_ids_check_validate(){
  var _is_scceuss = false;
  if( $("input[type='checkbox'][name='select_ids[]']").length > 0){
    $("input[type='checkbox'][name='select_ids[]']:checked").each(function(index, value){
      if(_is_scceuss===true) return ;
      var val = $(this).val();
      if(!util.isEmpty(val)) _is_scceuss = true;
    });
  }
  if(!_is_scceuss){
    front.showValidateError('#check_list', '削除対象を１つ以上選択してください');
  }

  return _is_scceuss;
}
</script>
@endsection
@section('third_form')
@endsection
@section('confirm_form')
@endsection
