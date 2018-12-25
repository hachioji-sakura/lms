<div class="card card-primary">
  <div class="card-body p-0">
    <!-- THE CALENDAR -->
    <div id="calendar"></div>
  </div>
</div>

<script>
  $(function () {
    function set_calendar(start_time, end_time, callback) {
      {{$set_calendar}}
      return;
    }

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear();
    var current_hours = date.getHours() - 5;
    var current_minutes = date.getMinutes();
    var first_scroll_time = "15:00:00";
    const $calendar = $('#calendar').fullCalendar({
      header    : {
        left  : 'prev today',
        center: 'title',
        right : 'month,agendaWeek,agendaDay next'
      },
      columnFormat: {
        month: 'ddd', // 月
        week: 'D[(]ddd[)]', // 7(月)
        day: 'D[(]ddd[)]' // 7(月)
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
      editable  : false,
      droppable : false, // this allows things to be dropped onto the calendar !!!
      // 選択可
      selectable: false,
      dayClick: function(date, allDay, jsEvent, view) {
      },
      select: function(start, end, jsEvent, view , resource){
        var _lesson_time = end.diff(start, 'minutes');
        $calendar.fullCalendar('unselect');
        var param ="?_page_origin={{$domain}}_{{$user_id}}";
        param += "&start_date="+start.year()+'-'+(start.month()+1)+'-'+start.day();
        param += "&start_hours="+start.hours();
        param += "&start_minutes="+start.minutes();
        param += "&lesson_time="+_lesson_time;

        base.showPage('dialog', "subDialog", "授業追加", "/calendars/create"+param);
      },
      eventDrop: function(event, delta, revertFunc) {
        console.log(event.title + " was dropped on " + event.start.format());s
      },
      {{$event_click}}
      allDayText:'終日',
      axisFormat: 'H(:mm)',
      //defaultView: 'agendaWeek',
      scrollTime: first_scroll_time,
      // 最小時間
      minTime: "10:00:00",
      // 最大時間
      maxTime: "22:00:00",
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
      eventRender: function(events, element) {
        console.log(events.type);
        var bgcolor = "#66D";
        var textColor = "#fff";
        var icon = '';
        var title = '予定追加';
        if(!util.isEmpty(events.title)) title = events.title;
        switch(events.type){
          case "cancel":
            bgcolor = "#666";
            icon = '<i class="fa fa-times mr-1"></i>';
            break;
          case "exchange":
            bgcolor = "#6D6";
            icon = '<i class="fa fa-exchange-alt mr-1"></i>';
            break;
          case "study":
          default:
            bgcolor = "#66D";
            icon = '<i class="fa fa-chalkboard-teacher mr-1"></i>';
            break;
        }
        $(element[0])
          .css('color', textColor)
          .css('border-color', bgcolor)
          .css('background-color', bgcolor)
          .css('cursor', 'pointer')
          .html('<div class="p-1 text-center">'+icon+'<span class="d-none d-sm-inline-block">'+title+'</span></div>');

          /*
      	if(events.img){
    	    $(element[0])
      	    .css("border-color", "transparent")
      	    .css("background-color", "transparent")
      	    .html('<img class="photo"  src="'+events.img+'" width=32 height=32/>');
      	}
        */
      },
      events: function(start, end, timezone, callback) {
        set_calendar(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), callback);
      },
      drop      : function (date, allDay) {
        console.log('drop');
        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject')

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject)

        // assign it the date that was reported
        copiedEventObject.start           = date
        copiedEventObject.allDay          = allDay
        copiedEventObject.backgroundColor = $(this).css('background-color')
        copiedEventObject.borderColor     = $(this).css('border-color')

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove()
        }

      }
    })
    // 動的にオプションを変更する
    $('#calendar').fullCalendar('option', 'height', 'auto');
  })
</script>
