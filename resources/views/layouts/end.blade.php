@section('end')
<!-- layouts.end start-->
<script>
$(function(){
  $('a[href^="#"][scroll]').click(function() {
    var href= $(this).attr("href");
    var scroll= $(this).attr("scroll");
    var target = $(href == "#" || href == "" ? 'html' : href);
    if(target.length>0){
      var position = target.offset().top;
      $('body,html').animate({scrollTop:position}, scroll, 'swing', function(){
      });
    }
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
<script src="{{asset('js/base/util.js?v=3')}}"></script>
<script src="{{asset('js/base/cardTable.js')}}"></script>
<script src="{{asset('js/base/listTable.js')}}"></script>
<script src="{{asset('js/base/fileUI.js')}}"></script>
<script src="{{asset('js/base/dom.js?v=1')}}"></script>
<script src="{{asset('js/base/service.js?v=3')}}"></script>
<script src="{{asset('js/base/front.js')}}"></script>
<script src="{{asset('js/base/translate.js')}}"></script>
<script src="{{asset('js/base/base.js?v=4')}}"></script>
</body>
<script>
$(function(){
  var _timer = null;
  $(window).on('scroll',function(){
    var heroBottom = $('.main-header').height();
    $('.main-header').css('top', 0);
    if($(window).scrollTop() > heroBottom){
      if(_timer!=null) clearTimeout(_timer);
      _timer = setTimeout(header_scroll, 300);
    }
  });
  function header_scroll(){
    var heroBottom = $('.main-header').height();
    if($(window).scrollTop() > heroBottom){
      $('.main-header').animate({'top' : $(window).scrollTop()},'fast', function(){
        if($(window).scrollTop() > heroBottom){
          $('.main-header').css('top', $(window).scrollTop());
        }
      });
    }
    else {
      $('.main-header').css('top', 0);
    }
  }
  $(window).trigger('scroll');
  //キーワード検索
  $("#search_button").on("click", function(e){
    var _search_word = $("input[name=search_word]").val();
    if(!util.isEmpty(_search_word)){
      location.href = service.setQueryParam({"search_word" : _search_word});
    }
    else {
      location.href = service.setQueryParam({"search_word" : ""});
    }
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("keypress", function(e){
    if(e.keyCode==13){
      //検索入力～Enterで、検索ボタン押下
      $("#search_button").click();
    }
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("focusin", function(e){
    $(this).animate({width:"260px"},500,"easeInOutExpo");
  });
  $("input[name='search_word'][accesskey='keyword_search']").on("focusout", function(e){
    $(this).animate({width:"140px"},500,"easeInOutExpo");
  });
  //ダイアログでサブページを開く場合、
  $("a[page_url][page_title][page_form=dialog]").on("click", function(e){
    console.log($(this).attr('page_title'));
    base.showPage("dialog", "subDialog", $(this).attr("page_title"), $(this).attr("page_url"));
  });
  //フッターから出てくるタイプのフォーム
  $("a[page_url][page_title][page_form='footer_form'], a.nav-link[page_url][page_title][page_form='footer_form']").on("click", function(e){
    base.showPage("footer", "footer_form", $(this).attr("page_title"), $(this).attr("page_url"));
  });
  // #で始まるアンカーをクリックした場合に処理
  $("a[href^='#'][scroll]").on("click", function(){
    var speed = 400; // ミリ秒
	  // アンカーの値取得
	  var href= jQuery(this).attr("href");
	  // 移動先を取得
	  var target = jQuery(href == "#" || href == "" ? 'html' : href);
	  // 移動先を数値で取得
    if(target.length>0){
      var h = target.height();
  	  var position = target.offset().top - (h/2);
  	  // スムーススクロール
  	  jQuery('body,html').animate({scrollTop:position}, speed, 'swing');
    }
    return false;
  });
});
function status_style(status){
  var _ret = {
    "rest" : {
      "color" : "#dc3545",
      "icon" : "<i class='fa fa-calendar-times mr-1'></i>",
      "style" : "secondary",
    },
    "rest_cancel" : {
      "color" : "#ffc107",
      "icon" : "<i class='fa fa-hourglass-half mr-1'></i>",
    },
    "lecture_cancel" : {
      "color" : "#6c757d",
      "icon" : "<i class='fa fa-ban mr-1'></i>",
      "style" : "secondary",
    },
    "absence" : {
      "color" : "#dc3545",
      "icon" : "<i class='fa fa-user-times mr-1'></i>",
      "style" : "danger",
    },
    "confirm" : {
      "color" : "#fd7e14",
      "icon" : "<i class='fa fa-question-circle mr-1'></i>",
      "style" : "warning",
    },
    "fix" : {
      "color" : "#17a2b8",
      "icon" : "<i class='fa fa-clock mr-1'></i>",
      "style" : "primary",
    },
    "presence" : {
      "color" : "#28a745",
      "icon" : "<i class='fa fa-check-circle mr-1'></i>",
      "style" : "success",
    },
    "trial" : {
      "color" : "#e83e8c",
      "icon" : "<i class='fa fa-exclamation-circle mr-1'></i>",
      "style" : "warning",
    },
    "new" : {
      "color" : "#ffc107",
      "icon" : "<i class='fa fa-calendar-plus mr-1'></i>",
      "style" : "secondary",
    },
    "cancel" : {
      "color" : "#6c757d",
      "icon" : "<i class='fa fa-ban mr-1'></i>",
      "style" : "secondary",
    },
  };
  if(_ret[status]) return _ret[status];
  return _ret['trial'];
}


</script>

</html>
<!-- layouts.end end-->
@endsection
