@section('contents')
    <div class="card-header">
      <div class="card-tools">
        <div class="input-group input-group-sm" style="">
          <input type="text" name="search_word" class="form-control float-right" placeholder="Search" value="{{$search_word}}">
          <div class="input-group-append">
            <button type="submit" class="btn btn-default" id="search_button">
              <i class="fa fa-search"></i>
            </button>
          </div>
          <!--
          <a type="button" class="btn btn-primary btn-sm" href="#">
            <i class="fa fa-plus"></i>
            <span class="btn-label">追加</span>
          </a>
          -->
        </div>
      </div>
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      <div id="listTable" class="card-body card-list" alt="CardTable">
        @if(count($items) > 0)
        <ul class="mailbox-attachments clearfix row">
          @include('components.tiles', [
            'items'=>$items
            ])
        </ul>
        @else
        データがありません。
        @endif
      </div>
    </div>
<script>
$(function(){
  $("#search_button").on("click", function(e){
    var _search_word = $("input[name=search_word]").val();
    if(!util.isEmpty(_search_word)){
      location.href="./@yield('domain')?search_word="+_search_word;
    }
  });
});
</script>
@endsection
