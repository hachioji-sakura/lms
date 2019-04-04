@section('end')
<!-- layouts.end start-->
<script>
$(function(){
  $('a[href^="#"][scroll]').click(function() {
    var href= $(this).attr("href");
    var scroll= $(this).attr("scroll");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top;
    $('body,html').animate({scrollTop:position}, scroll, 'swing', function(){
    });
  });
  $('.toggle-btn').click(function() {
    //指定したidを閉じたり開いたり
    var id = $(this).attr("target");
    var _btn = $(this);
    $('#'+id).slideToggle("fast", function(){
      if($(this).is(":hidden")){
        $(".toggle-btn-open", _btn).show();
        $(".toggle-btn-close", _btn).hide();
      }
      else {
        $(".toggle-btn-open", _btn).hide();
        $(".toggle-btn-close", _btn).show();
      }
    });
  });
});
</script>
<script src="{{asset('js/lib/utf.js')}}"></script>
<script src="{{asset('js/lib/base64.js')}}"></script>
<script src="{{asset('js/lib/inflate.js')}}"></script>
<script src="{{asset('js/lib/deflate.js')}}"></script>
<script src="{{asset('js/lib/timsort.js')}}"></script>
<script src="{{asset('js/base/util.js')}}"></script>
<script src="{{asset('js/base/cardTable.js')}}"></script>
<script src="{{asset('js/base/listTable.js')}}"></script>
<script src="{{asset('js/base/fileUI.js')}}"></script>
<script src="{{asset('js/base/dom.js')}}"></script>
<script src="{{asset('js/base/service.js')}}"></script>
<script src="{{asset('js/base/front.js')}}"></script>
<script src="{{asset('js/base/base.js')}}"></script>
</body>
</html>
<!-- layouts.end end-->
@endsection
