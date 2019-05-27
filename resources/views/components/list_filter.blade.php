<div id="filter_form" class="container-fluid">
  <form method="POST" action="{{request()->fullUrl()}}">
    @method('GET')
    @csrf
    <input name="_domain" type="hidden" value="{{$domain}}">
    <input name="_page" type="hidden" value="{{$_page}}">
    <input name="_line" type="hidden" value="{{$_line}}">
    <div class="row collapse p-2" id="filter_form_item">
      {{$search_form}}
    </div>
    <div class="col-12 mt-2 text-right">
        <button type="button" class="btn btn-submit btn-info mr-2"  accesskey="filter_search">
          <i class="fa fa-search mr-1"></i>
            絞り込み
        </button>
        <button type="reset" class="btn btn-secondary" accesskey="filter_search">
            クリア
        </button>
    </div>
  </div>
  </form>
</div>

<script>
$(function(){
  base.pageSettinged('filter_form', null);
  $("a.page-link[accesskey='pager']").on('click', function(){
    var page = $(this).attr("page");
    $("input[name=_page]").val(page);
    $("#filter_form form").submit();
  });
  $("button[accesskey='filter_search'][type=button]").on('click', function(e){
    $("input[name=_page]").val("1");
    $("#filter_form form").submit();
  });
  $("button[accesskey='filter_search'][type=reset]").on('click', function(e){
    e.preventDefault();
    $("#filter_form form select option").attr('selected', false);
    front.clearFormValue('filter_form');
    $("input[name=_page]").val("1");
  });
});
</script>
