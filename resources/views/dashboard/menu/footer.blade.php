@section('footer')
<div id="footer_form" class="card card-primary card-outline collapse footer-form">
  <div class="card-header">
    <h3 class="card-title"></h3>
  </div>
  <div class="card-body">
  </div>
</div>
<dl class="btn-group">
    @yield('page_footer')
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
  //ダイアログでサブページを開く場合、
  $("a[page_url][page_title][page_form=dialog]").on("click", function(e){
    $("#subDialog .content-sub-title").html($(this).attr("page_title"));
    $("#subDialog .content-sub-body").load($(this).attr("page_url"), function(){
      base.pageSettinged('subDialog form', null);
      //サブページ内のsubmit
      $("#subDialog .btn[type=submit][accesskey]").on("click", function(){
        var form = "subDialog .content-sub-body form";
        if(!front.validateFormValue(form)) return false;
        $("#"+form).submit();
      });
      //サブページ内のreset
      $("#subDialog .btn[type=reset]").on("click", function(){
        $("#subDialog").modal('hide');
      });
      base.pageOpen('subDialog');
    });
  });
  //フッターから出てくるタイプのフォーム
  $("a[page_url][page_title][page_form='footer_form'], a.nav-link[page_url][page_title][page_form='footer_form']").on("click", function(e){
      $("#footer_form .card-title").html($(this).attr("page_title"));
      $('#footer_form .card-body').load($(this).attr("page_url"), function(){
        base.pageSettinged('footer_form form', null);
        $('.footer-form.show').collapse('hide');
        //サブページ内のsubmit
        $("#footer_form .btn[type=submit][accesskey]").on("click", function(){
          var form = "footer_form .card-body form";
          if(!front.validateFormValue(form)) return false;
          $("#"+form).submit();
        });
        //サブページ内のreset
        $("#footer_form .btn[type=reset]").on("click", function(){
          $('#footer_form').collapse('hide');
        });
        $('#footer_form').collapse('show');
      });
  });
});
</script>
@endsection
