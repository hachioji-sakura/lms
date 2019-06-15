<div class="card card-primary">
  <div class="card-body p-0">
    <!-- THE CALENDAR -->
    @if(isset($id) && $id >0)
      <div id="calendar{{$id}}"></div>
    @else
      <div id="calendar"></div>
    @endif
  </div>
</div>

<script>
  $(function () {
    function status_style(status){
      var _ret = {
        "trial" : {
          "color" : "#6c757d",
          "icon" : "<i class='fa fa-calendar-plus mr-1'></i>",
        },
        "new" : {
          "color" : "#6c757d",
          "icon" : "<i class='fa fa-calendar-plus mr-1'></i>",
        },
        "confirm" : {
          "color" : "#ffc107",
          "icon" : "<i class='fa fa-question-circle mr-1'></i>",
        },
        "fix" : {
          "color" : "#17a2b8",
          "icon" : "<i class='fa fa-clock mr-1'></i>",
        },
        "cancel" : {
          "color" : "#6c757d",
          "icon" : "<i class='fa fa-ban mr-1'></i>",
        },
        "presence" : {
          "color" : "#28a745",
          "icon" : "<i class='fa fa-check-circle mr-1'></i>",
        },
        "absence" : {
          "color" : "#dc3545",
          "icon" : "<i class='fa fa-user-times mr-1'></i>",
        },
        "rest" : {
          "color" : "#dc3545",
          "icon" : "<i class='fa fa-calendar-times mr-1'></i>",
        },
        "rest_cancel" : {
          "color" : "#ffc107",
          "icon" : "<i class='fa fa-hourglass-half mr-1'></i>",
        },
        "lecture_cancel" : {
          "color" : "#6c757d",
          "icon" : "<i class='fa fa-ban mr-1'></i>",
        },
      };
      if(_ret[status]) return _ret[status];
      return _ret['new'];
    }
    function event_render(events, element, title){
      var _status_style = status_style(events.status);
      if(events.status=="confirm" && events.total_status=="fix"){
        _status_style = status_style(events.status);
      }
      else if(events.status=="confirm"){
        _status_style = status_style(events.total_status);
      }
      if(events.status=="fix" && events.total_status=="rest"){
        _status_style = status_style(events.total_status);
      }
      var bgcolor = _status_style["color"];
      var icon = _status_style["icon"];
      var textColor  = "#FFF";
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
        .css('border-color', bgcolor)
        .css('background-color', bgcolor)
        .css('cursor', 'pointer')
        .html('<div class="p-1 text-center">'+icon+t1+'<span class="d-none d-sm-inline-block">'+t2+'</span></div>');
      /*
      if(events.img){
        $(element[0])
          .css("border-color", "transparent")
          .css("background-color", "transparent")
          .html('<img class="photo"  src="'+events.img+'" width=32 height=32/>');
      }
      */
    }
    function set_calendar(start_time, end_time, callback) {
      start_time = start_time.replace_all('-','');
      end_time = end_time.replace_all('-','');
      service.getAjax(false, '/api_calendars/{{$user_id}}/'+start_time+'/'+end_time, null,
        function(result, st, xhr) {
          if(result['status']===200){
            var events = [];
            $.each(result['data'], function(index, value) {
              value["start"] = value['start_time'];
              value["end"] = value['end_time'];
              value["total_status"] = value.status;
              value["status"] = value.own_member.status;
              events.push(value);
            });
            console.log(events);
            if(util.isFunction(callback))callback(events);
          }
        },
        function(xhr, st, err) {
            messageCode = "error";
            messageParam= "\n"+err.message+"\n"+xhr.responseText;
            alert("カレンダー取得エラー\n画面を再表示してください\n"+messageParam);
        }
      ,true);
      return;
    }

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

    var calendar_option = {
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
          right : 'month,agendaWeek,agendaDay next'
        @endif
      },
      columnFormat: {
        month: 'ddd', // 月
        week: 'D[\n(]ddd[)]', // 7(月)
        day: 'D[\n(]ddd[)]' // 7(月)
      },
      // タイトルの書式
      titleFormat: {
        month: 'YYYY年M月',
        week: "YYYY年M月",
        day: ' M月D日[(]ddd[)]',
      },
      //week: 'M月 D日',
      // ボタン文字列
      buttonText: {
          prev:     '＜',
          next:     '＞',
          prevYear: '前年',
          nextYear: '次年',
          today:    '今日',
          month:    '月',
          week:     '週',
          day:      '日'
      },
      nowIndicator : true,
      editable  : false,
      droppable : false, // this allows things to be dropped onto the calendar !!!
      dayClick: function(date, allDay, jsEvent, view) {
        console.log('dayClick'+date);
        $calendar.fullCalendar('gotoDate', date);
        //$calendar.fullCalendar('select',date,date);
      },
      {{$event_select}}
      eventDrop: function(event, delta, revertFunc) {
        console.log(event.title + " was dropped on " + event.start.format());s
      },
      {{$event_click}}
      allDaySlot: false,
      //allDayText:'終日',
      axisFormat: 'H(:mm)',
      @if(isset($defaultDate) && !empty($defaultDate))
        defaultDate : '{{$defaultDate}}',
      @endif
      @if(isset($mode) && $mode==="day")
        defaultView: 'agendaDay',
      @elseif(isset($mode) && $mode==="week")
        defaultView: 'agendaWeek',
      @else
        defaultView: 'agendaWeek',
      @endif
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
      dayNames: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'],
      // 曜日略称
      dayNamesShort: ['日', '月', '火', '水', '木', '金', '土'],
      // 選択時にプレースホルダーを描画
      selectHelper: true,
      // 自動選択解除
      unselectAuto: true,
      // 自動選択解除対象外の要素
      unselectCancel: '',
      {{$event_render}}
      events: function(start, end, timezone, callback) {
        start = start.format('YYYY-MM-DD');
        end = end.format('YYYY-MM-DD');
        set_calendar(start, end, callback);
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

    const $calendar = $('#'+id).fullCalendar(calendar_option);

    // 動的にオプションを変更する
    $calendar.fullCalendar('option', 'height', 'auto');
    @if(isset($mode) && $mode==="day")
    $('.fc-toolbar').hide();
    @endif
    window.onpopstate=function(e){
      //prev , nowなどの操作後にhistory.backした際の表示
      console.log(e.state);
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
