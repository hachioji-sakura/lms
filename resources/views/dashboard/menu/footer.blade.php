@section('footer')
@yield('page_footer_form')
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
  //ダイアログでサブページを開く
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
})
</script>
@endsection
