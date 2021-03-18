@section('title')
  {{__('labels.calendar_page')}}
@endsection
@extends('dashboard.common')

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="/{{$domain}}/{{$item->id}}/schedules" class="nav-link @if($view=="schedules") active @endif">
      <i class="fa fa-tasks nav-icon"></i>予定一覧
    </a>
  </li>
  </li>
</ul>
@endsection

@section('page_footer')
{{--
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/lesson_request_calendars/create?teacher_id={{$item->id}}" page_title="{{__('labels.schedule_add')}}">
    <i class="fa fa-chalkboard-teacher"></i>{{__('labels.schedule_add')}}
  </a>
</dt>
--}}
@endsection

@section('calendar')
<div class="card card">
  <div class="card-body p-0">
    <input type="hidden" name="event_id" value="{{$item->id}}" />
    <div id="calendar"></div>
  </div>
</div>

<script>
  var $calendar;
  var is_search = false;
  $(function(){
    $('#filter_form').on('hidden.bs.modal', function () {
      if(is_search==true){
        is_search = false;
        set_calendar(function(events){
          $calendar.fullCalendar('removeEvents');
          $calendar.fullCalendar('addEventSource', events);
          $calendar.fullCalendar('rerenderEvents');
        });
      }
    });
    base.pageSettinged('filter_form', null);
    $("a.page-link[accesskey='pager']").on('click', function(){
      var page = $(this).attr("page");
      $("input[name=_page]").val(page);
      //subDialog側にformが残っているとsubmitされる対策
      $("#subDialog .modal-dialog").remove();
      $(this).prop("disabled",true);
      $("#filter_form form.filter").submit();
    });
    $("button[accesskey='filter_search'][type=button]").on('click', function(e){
      is_search = true;
      $('#filter_form').modal('hide');
    });
    $("button[accesskey='filter_search'][type=reset]").on('click', function(e){
      e.preventDefault();
      $("#filter_form form select option").attr('selected', false);
      front.clearFormValue('filter_form');
    });
  });
  function event_create(start, end, jsEvent, view , resource){
    var _course_minutes = end.diff(start, 'minutes');
    $calendar.fullCalendar("removeEvents", -1);
    $calendar.fullCalendar('unselect');
    $calendar.fullCalendar('addEventSource', [{
      id:-1,
      title: "{{__('labels.schedule_add')}}",
      start: start,
      end : end,
      status : "new",
      @if($domain=='teachers')
      teaching_type : "add",
      schedule_type_code : "new",
      selected : true,
      @endif
    }]);

    var start_date = util.format("{0}/{1}/{2}", start.year(), (start.month()+1) , start.date());
    var end_date = util.format("{0}/{1}/{2}", end.year(), (end.month()+1) , end.date());
    var param ="";
    @if($domain=='teachers')
    param += "?teacher_id={{$item->id}}";
    @elseif($domain=='managers')
    param += "?manager_id={{$item->id}}";
    @endif
    param += "&start_date="+start_date;
    param += "&start_hours="+start.hour();
    param += "&start_minutes="+start.minute();
    param += "&end_date="+end_date;
    param += "&end_hours="+end.hour();
    param += "&end_minutes="+end.minute();
    param += "&course_minutes="+_course_minutes;
    base.showPage('dialog', "subDialog", "{{__('labels.schedule_add')}}", "/lesson_request_calendars/create"+param, function(){
      $calendar.fullCalendar("removeEvents", -1);
    });
  }
  function event_render(events, element, title, view_mode){
    var _status_style = status_style(events.status);
    if(events.teaching_type=='training'){
      _status_style = status_style('training');
    }
    /*
    if(view_mode=='students'){
      _status_style = status_style(events.status);
    }
    else {
      console.log(events.teaching_type);
      if(events.teaching_type=='season_training' || events.teaching_type=='training'){
        _status_style = status_style('training');
      }
      else {
        _status_style = status_style(events.total_status);
      }
    }
    */
    if(events.total_status=="rest" || events.total_status=="lecture_cancel" ||
      //全体ステータス側が優先
      events.total_status=="cancel"){
      _status_style = status_style(events.total_status);
    }
    var bgcolor = _status_style["color"];
    var icon = _status_style["icon"];
    var textColor  = _status_style["textcolor"];
    var length = 5;
    //一文字分表示する
    /*
    var t1 = title.substring(0,length);
    var t2 = title.substring(length,title.length);
    */
    var t1=title;
    var t2="";
    $(element[0])
      .css('color', textColor)
      .css('border-color', "#FFFFFF")
      .css('background-color', bgcolor)
      .css('cursor', 'pointer')
      .html('<div class="p-1 text-center">'+icon+t1+'<span class="d-none d-sm-inline-block">'+t2+'</span></div>');

    if(events.selected==true){
      $(element[0]).css('filter', 'drop-shadow(4px 4px 4px rgba(60,60,60,0.6)) opacity(50%)')
    }
    /*
    if(events.img){
      $(element[0])
        .css("border-color", "transparent")
        .css("background-color", "transparent")
        .html('<img class="photo"  src="'+events.img+'" width=32 height=32/>');
    }
    */
  }
  function set_calendar(callback) {
    var is_all = $('input[name="is_all"]:checked').val();
    if(is_all==1){
      user_id = 0;
    }

    //'/api_lesson_request_calendars/'+user_id+'/'+start_time+'/'+end_time
    var form_data = front.getFormValue('filter_form');
    form_data['loading'] = true;
    var event_id = $('input[name="event_id"]').val();
    var url = '/api_lesson_request_calendars?event_id='+event_id+'&is_fix_status=1';
    service.getAjax(false, url, form_data,
      function(result, st, xhr) {
        if(result['status']===200){
          var events = result['data'];
          console.log(events);
          if(util.isFunction(callback))callback(events);
        }
      },
      function(xhr, st, err) {
          messageCode = "error";
          messageParam= "\n"+err.message+"\n"+xhr.responseText;
          alert("カレンダー取得エラー\n画面を再表示してください\n"+messageParam);
      }
    ,false);
    return;
  }

  $(function () {
    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    /*
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear();
    var current_hours = date.getHours() - 5;
    var current_minutes = date.getMinutes();
    */
    var first_scroll_time = "15:00:00";
    @if(isset($id) && $id >0)
    var id= "calendar{{$id}}";
    @else
    var id= "calendar";
    @endif

    var _defaultView = "agendaWeek";
    @if(isset($mode) && $mode==="day")
     _defaultView = "agendaDay";
    @elseif(isset($mode) && $mode==="week")
    @else
    if(screen.width < 768) {
      _defaultView = "agendaDay";
    }
    @endif

    @if($domain=='managers' || $domain=='teachers')
    var _right_button = "month,agendaWeek,agendaDay,filter next";
    @else
    var _right_button = "month,agendaWeek,agendaDay next";
    @endif


    var calendar_option = {
      customButtons:{
          filter:{
              text: '<i class="fa fa-filter text-sm"></i>',
              click:function(){
                $('#filter_form').modal('show');
              }
          }
      },
      header    : {
        @if(isset($mode) && $mode==="day")
          left  : '',
          center: '',
          right : ''
        @elseif(isset($mode) && $mode==="week")
          left  : '',
          center: 'title',
          right : ''
        @else
          left  : 'prev today',
          center: 'title',
          right : _right_button
        @endif
      },
      columnFormat: {
        month: 'ddd', // 月
        week: 'D[\n(]ddd[)]', // 7(月)
        day: 'D[\n(]ddd[)]' // 7(月)
      },
      // タイトルの書式
      titleFormat: {
        month: 'YYYY/M',
        week: "YYYY/M",
        day: ' M/D[(]ddd[)]',
      },
      //week: 'M月 D日',
      // ボタン文字列
      buttonText: {
          prev:     '＜',
          next:     '＞',
          prevYear: '{{__('labels.calendar_button_prev_year')}}',
          nextYear: '{{__('labels.calendar_button_next_year')}}',
          today:    '{{__('labels.calendar_button_today')}}',
          month:    '{{__('labels.calendar_button_month')}}',
          week:     '{{__('labels.calendar_button_week')}}',
          day:      '{{__('labels.calendar_button_day')}}'
      },
      nowIndicator : true,
      editable  : false,
      droppable : false, // this allows things to be dropped onto the calendar !!!
      dayClick: function(date, allDay, jsEvent, view) {
        $calendar.fullCalendar('gotoDate', date);
        //$calendar.fullCalendar('select',date,date);
      },
      eventClick: function(event, jsEvent, view) {
        $calendar.fullCalendar('unselect');
        if(event.id<0){
          event_create(event.start, event.end, jsEvent, view);
          return false;
        }
        if(event.work==5 || event.work==11){
          //演習の場合は操作不要
          base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/lesson_request_calendars/"+event.id);
        }
        else {
          switch(event.total_status){
            case "new":
              base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/lesson_request_calendars/"+event.id+"/status_update/confirm");
              break;
            case "confirm":
              //生徒へ再送
              base.showPage('dialog', "subDialog", "{{__('labels.schedule_remind')}}", "/lesson_request_calendars/"+event.id+"/status_update/remind");
              break;
            case "fix":
              if(event.is_passed==true){
                //過ぎていたら出欠
                base.showPage('dialog', "subDialog", "{{__('labels.schedule_presence')}}", "/lesson_request_calendars/"+event.id+"/status_update/presence");
              }
              else{
                //過ぎていないなら休み取り消し
                base.showPage('dialog', "subDialog", "{{__('labels.ask_lecture_cancel')}}", "/lesson_request_calendars/"+event.id+"/status_update/lecture_cancel");
              }
              break;
            case "absence":
            case "presence":
              base.showPage('dialog', "subDialog", "{{__('labels.calendar_button_attendance')}}{{__('labels.edit')}}", "/lesson_request_calendars/"+event.id+"/status_update/presence");
              break;
            case "rest":
            case "cancel":
            default:
              base.showPage('dialog', "subDialog", "{{__('labels.schedule_details')}}", "/lesson_request_calendars/"+event.id);
              break;
          }
        }
      },
      eventDrop: function(event, delta, revertFunc) {
        console.log(event.title + " was dropped on " + event.start.format());s
      },
      // 選択可
      @if($user->role=='manager')
      selectable: true,
      select: function(start, end, jsEvent, view , resource){
        event_create(start, end, jsEvent, view , resource);
      },
      @endif
      allDaySlot: false,
      //allDayText:'終日',
      axisFormat: 'H(:mm)',
      defaultDate : '{{$item->event_from_date}}',
      defaultView: _defaultView,
      scrollTime: first_scroll_time,
      // 最小時間
      @if(isset($minHour) && $minHour>0)
        @if($minHour>15)
          minTime: "15:00:00",
        @else
          minTime: "10:00:00",
        @endif
      @else
        minTime: "08:00:00",
      @endif
      // 最大時間
      @if(isset($maxHour) && $maxHour>0)
        @if($maxHour>20)
          maxTime: "23:00:00",
        @else
          maxTime: "21:00:00",
        @endif
      @else
        maxTime: "22:00:00",
      @endif
      // 月名称
      monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      // 月略称
      monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      // 曜日名称
      dayNames: ['日曜', '月曜', '火曜', '水曜', '木曜', '金曜', '土曜'],
      // 曜日略称
      dayNamesShort: ['日', '月', '火', '水', '木', '金', '土'],
      // 選択時にプレースホルダーを描画
      selectHelper: true,
      // 自動選択解除
      unselectAuto: true,
      // 自動選択解除対象外の要素
      unselectCancel: '',
      eventRender: function(event, element) {
        console.log('event');
        if(!event['schedule_type_code']){
          event['schedule_type_code'] = "new";
        }
        var title = "{{__('labels.schedule_add')}}";
        var remark = '('+event['place_floor_name']+')<br>'+event['start_hour_minute']+'-'+event['end_hour_minute'];
        var view_mode = $('input[name="view_mode"]:checked').val();
        if(view_mode=='students'){
          user_name = event['user_name'];
        }
        title = event['student_name']+remark;
        event_render(event, element, title, view_mode);
      },
      events: function(start, end, timezone, callback) {
        start = start.format('YYYY-MM-DD');
        end = end.format('YYYY-MM-DD');
        /*
        start_time = start_time.replace_all('-','');
        end_time = end_time.replace_all('-','');
        */
        $('input[name="from_date"]').val(start);
        $('input[name="to_date"]').val(end);

        set_calendar(callback);
        var view = this.getView();
        var setting = {
          'start' : view.start.format('YYYY-MM-DD'),
          'end' : view.end.format('YYYY-MM-DD'),
          'type' : view.type
        };
        var q = util.convJsonToQueryString(setting);
        var url = location.pathname+"?"+q;
        if(_is_history===false){
          //履歴からの表示でなければ、履歴に追加
          history.pushState(setting, null, url);
        }
        _is_history = false;
      },
    };
    //URLパラメータより表示パラメータを取得（日付とview.type)
    var setting = util.convQueryStringToJson();
    if(!util.isEmpty(setting)){
      if(!util.isEmpty(setting.start)) calendar_option["defaultDate"] = setting.start;
      if(!util.isEmpty(setting.type)) calendar_option["defaultView"] = setting.type;
    }

    $calendar = $('#'+id).fullCalendar(calendar_option);

    // 動的にオプションを変更する
    $calendar.fullCalendar('option', 'height', 'auto');
    @if(isset($mode) && $mode==="day")
    $('.fc-toolbar').hide();
    @endif
    window.onpopstate=function(e){
      //prev , nowなどの操作後にhistory.backした際の表示
      _is_history = true;
      var setting = e.state;
      if(setting){
        if(setting.type) {
          $calendar.fullCalendar("changeView", setting.type);
        }
        if(setting.start){
          $calendar.fullCalendar('gotoDate', setting.start);
        }
      }
      else {
        //設定なし＝初期表示
        $calendar.fullCalendar("changeView", "agendaWeek");
        $calendar.fullCalendar('gotoDate', util.nowDate());
      }
    };
    var _is_history = false;
});
</script>

<div class="modal fade" id="filter_form" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title content-sub-title">{{__('labels.filter')}}</h4>
      </div>
      <div class="modal-body content-sub-body" id="calendar_filter">
        <input name="_domain" type="hidden" value="{{$domain}}">
        <input name="from_date" type="hidden" value="">
        <input name="to_date" type="hidden" value="">
        <input name="user_id" type="hidden" value="{{$item->user_id}}">
        <div class="row p-2" id="filter_form_item">
          @component('calendars.filter', ['domain' => $domain, 'attributes'=>$attributes, 'user'=>$user, 'item' => $item, 'filter' => $filter, 'is_list' => false])
          @endcomponent

          <div class="col-12 col-md-6 mb-2">
            <div class="form-group">
              <label for="label_setting" class="w-100">
                {{__('labels.label_setting')}}
              </label>
              <label class="mx-2">
                <input type="radio" value="students" name="view_mode" class="icheck flat-green"
                @if((isset($filter['calendar_filter']['view_mode']) && filter['calendar_filter']['view_mode']=='students') || $domain=='students')
                  checked
                @endif
                >{{__('labels.teacher_name_display')}}
              </label>
              <label class="mx-2">
                <input type="radio" value="teachers" name="view_mode" class="icheck flat-green"
                @if((isset($filter['calendar_filter']['view_mode']) && filter['calendar_filter']['view_mode']!='students') || $domain!='students')
                  checked
                @endif
                >{{__('labels.student_name_display')}}
              </label>
            </div>
          </div>
          <div class="col-12 col-md-6 mb-2">
            <div class="form-group">
              <label for="target_data" class="w-100">
                {{__('labels.target_data')}}
              </label>
              <label class="mx-2">
              <input type="radio" value="0" name="is_all_data" class="icheck flat-green"
              @if(!(isset($filter['calendar_filter']['is_all_data']) && $filter['calendar_filter']['is_all_data']==1))
                checked
              @endif
              >{{__('labels.user_only')}}
              </label>
              <label class="mx-2">
              <input type="radio" value="1" name="is_all_data" class="icheck flat-red"
              @if(isset($filter['calendar_filter']['is_all_data']) && $filter['calendar_filter']['is_all_data']==1)
                checked
              @endif
              >{{__('labels.all')}}
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 mt-2 text-right">
              <button type="button" class="btn btn-info mr-2" accesskey="filter_search">
                <i class="fa fa-search mr-1"></i>
                  {{__('labels.filter')}}
              </button>
              <button type="reset" class="btn btn-secondary" accesskey="filter_search">
                {{__('labels.clear')}}
              </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('contents')
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
        @yield('calendar')
			</div>
		</div>
	</div>
</section>
@endsection
