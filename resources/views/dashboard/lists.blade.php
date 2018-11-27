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
    <div class="card-body table-responsive p-0">
      @if(count($items) > 0)
      <table class="table table-hover">
        <tbody>
          @include('components.lists', [
            'items'=>$items
            ])
        </tbody>
      </table>
      @else
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
      </div>
      @endif
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
