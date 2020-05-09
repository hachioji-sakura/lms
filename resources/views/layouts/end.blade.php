@section('end')
<!-- layouts.end start-->
<script>
function status_style(status){
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
    "new" : {
      "color" : "#ffc107",
      "icon" : "<i class='fa fa-calendar-plus mr-1'></i>",
    },
    "cancel" : {
      "color" : "#6c757d",
      "icon" : "<i class='fa fa-ban mr-1'></i>",
    },
  };
  @foreach(config('attribute.calendar_status') as $id => $name)
  if(status_style["{{$id}}"]) status_style["{{$id}}"]["name"] = "{{$name}}";
  @endforeach
  @foreach(config('status_style') as $id => $name)
  if(status_style["{{$id}}"]) status_style["{{$id}}"]["style"] = "{{$name}}";
  @endforeach
  if(status_style[status]) return status_style[status];
  return status_style['trial'];
}
</script>
<script src="{{asset('js/lib/utf.js')}}"></script>
<script src="{{asset('js/lib/base64.js')}}"></script>
<script src="{{asset('js/lib/inflate.js')}}"></script>
<script src="{{asset('js/lib/deflate.js')}}"></script>
<script src="{{asset('js/lib/timsort.js')}}"></script>
<script src="{{asset('js/base/util.js?v=3')}}"></script>
<script src="{{asset('js/base/fileUI.js')}}"></script>
<script src="{{asset('js/base/dom.js?v=1')}}"></script>
<script src="{{asset('js/base/service.js?v=5')}}"></script>
<script src="{{asset('js/base/front.js')}}"></script>
<script src="{{asset('js/base/base.js?v=6')}}"></script>
<script src="{{ asset('/js/common.js') }}"></script>
</body>

</html>
<!-- layouts.end end-->
@endsection
