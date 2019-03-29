<div id="footer_form" class="card card-primary card-outline collapse footer-form">
  <div class="card-header">
    <h3 class="card-title page_title"></h3>
  </div>
  <div class="card-body page_contents">
  </div>
</div>
<dl class="btn-group">
  {{$setting}}
</dl>
<script>
$(function(){
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
  $("input[name='search_word']").on("keypress", function(e){
    if(e.keyCode==13){
      //検索入力～Enterで、検索ボタン押下
      $("#search_button").click();
    }
  });
  $("input[name='search_word']").on("focusin", function(e){
    $(this).animate({width:"260px"},500,"easeInOutExpo");
  });
  $("input[name='search_word']").on("focusout", function(e){
    $(this).animate({width:"140px"},500,"easeInOutExpo");
  });
  //ダイアログでサブページを開く場合、
  $("a[page_url][page_title][page_form=dialog]").on("click", function(e){
    base.showPage("dialog", "subDialog", $(this).attr("page_title"), $(this).attr("page_url"));
  });
  //フッターから出てくるタイプのフォーム
  $("a[page_url][page_title][page_form='footer_form'], a.nav-link[page_url][page_title][page_form='footer_form']").on("click", function(e){
    base.showPage("footer", "footer_form", $(this).attr("page_title"), $(this).attr("page_url"));
  });
});
</script>
