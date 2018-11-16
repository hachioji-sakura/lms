@extends('dashboard.common')
@include('dashboard.menu.navbar')
@include('students.lists.sidemenu')
@include('students.lists.footer')

@section('title', '生徒一覧')
@section('contents')
<section id="main" class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
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
                <a type="button" class="btn btn-primary btn-sm" href="/students/create">
                  <i class="fa fa-plus"></i>
                  <span class="btn-label">追加</span>
                </a>
                -->
              </div>
            </div>
            <h3 class="card-title">生徒一覧</h3>
            <div id="listTable" class="card-body card-list" alt="CardTable">
              <ul class="mailbox-attachments clearfix row">
                  @foreach($items as $item)
                  <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
                    <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
                    <input type="hidden" value="{{$item->id}}" name="id">
                    <div class="row">
                      <div class="col-12 text-center">
                        <img src="{{$item->icon}}" style="max-width:60%;">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 text-lg">
                        <a href="./students/{{$item->id}}">
                          <ruby style="ruby-overhang: none">
                            <rb>{{$item->name_last}} {{$item->name_first}}</rb>
                            <rt>{{$item->kana_last}} {{$item->kana_first}}</rt>
                          </ruby>
                        </a>
                      </div>
                  </li>
                  @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->
    </div>
  </div>
</section>
<script>
$(function(){
  $("#search_button").on("click", function(e){
    var _search_word = $("input[name=search_word]").val();
    if(!util.isEmpty(_search_word)){
      location.href="./students?search_word="+_search_word;
    }
  });
});
</script>
@endsection
