@section('end')
<!-- layouts.end start-->
<script>
const config_grade = @json(config('grade'));

function status_style(status){
  const _status_style = @json(config('status_style'));
  const _calendar_status = @json(config('attribute.calendar_status'));
  var status_style = {
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
    "absence" : {
      "color" : "#dc3545",
      "icon" : "<i class='fa fa-user-times mr-1'></i>",
    },
    "confirm" : {
      "color" : "#fd7e14",
      "icon" : "<i class='fa fa-question-circle mr-1'></i>",
    },
    "fix" : {
      "color" : "#17a2b8",
      "icon" : "<i class='fa fa-clock mr-1'></i>",
    },
    "presence" : {
      "color" : "#28a745",
      "icon" : "<i class='fa fa-check-circle mr-1'></i>",
    },
    "trial" : {
      "color" : "#e83e8c",
      "icon" : "<i class='fa fa-exclamation-circle mr-1'></i>",
    },
    "training" : {
      "color" : "#884898",
      "icon" : "<i class='fa fa-dumbbell mr-1'></i>",
    },
    "new" : {
      "color" : "#ffc107",
      "icon" : "<i class='fa fa-calendar-plus mr-1'></i>",
    },
    "cancel" : {
      "color" : "#6c757d",
      "icon" : "<i class='fa fa-ban mr-1'></i>",
    },
  };
  if(!status_style[status]) status='trial';
  if(_calendar_status[status]) status_style[status]["name"] = _calendar_status[status];
  if(_status_style[status]) status_style[status]["style"] = _status_style[status];
  return status_style[status];
}
</script>
<script src="{{asset('js/lib/utf.js')}}"></script>
<script src="{{asset('js/lib/base64.js')}}"></script>
<script src="{{asset('js/lib/inflate.js')}}"></script>
<script src="{{asset('js/lib/deflate.js')}}"></script>
<script src="{{asset('js/lib/timsort.js')}}"></script>
<script src="{{asset('js/base/util.js?v=3')}}"></script>
<script src="{{asset('js/base/fileUI.js')}}"></script>
<script src="{{asset('js/base/dom.js?v=2')}}"></script>
<script src="{{asset('js/base/service.js?v=5')}}"></script>
<script src="{{asset('js/base/front.js')}}"></script>
<script src="{{asset('js/base/base.js?v=10')}}"></script>
<script src="{{asset('js/common.js?v=3')}}"></script>
</body>

</html>
<!-- layouts.end end-->
@endsection
