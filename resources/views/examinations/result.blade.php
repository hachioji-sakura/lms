@section('title')
  @yield('domain_name')
@endsection
@extends('dashboard.common')

@section('contents')
<div class="card-header mb-4">
  <div class="row">
    <div class="col-12 text-sm mb-4" style="border-bottom:solid 1px #AAA;">
      <a href="/examinations">{{str_limit($textbook_title, 42,'...')}}</a> ＞ <a href="/examinations/{{$textbook_id}}">{{str_limit($chapter_title, 42,'...')}}</a>
    </div>
  </div>
  <h1 class="card-title">No{{$item['sort_no']}}.{{$item['title']}}</h1>
  @if($is_traning===1)
    <h4>練習モード</h4>
  @endif
  <div class="row">
    <div class="col-12">
      <h2>{{$item['body']}} <span class="text-primary" style="font-size:2rem;text-decoration:underline;">答え：{{$answer_text}}</span></h2>
    </div>
  </div>
</div>
<div class="card-body">
  <div class="row">
    <div class="col-12">
      @if($judge===1)
      <div class="col-12 text-success" style="font-size:2rem;">
        〇正解
      </div>
      @else
      <div class="col-12 text-danger" style="font-size:2rem;">
        ×不正解
      </div>
      <div class="col-12 text-danger" style="font-size:1.2rem;text-decoration:underline;">
        答え：<span style="color:#f00;">{{$item['answer_text']}}</span><br>
      </div>
      @endif
    </div>
  </div>
  <div class="row">
    <div class="col-12 mt-4">
      @if($judge===1)
        @if(isset($result))
          <h3>{{$chapter_title}} にすべて回答しました。</h3>
          <div class="row my-2">
              <div class="col-12">
                <h4 class="text-primary">正解/問題：{{$result['success']}} / {{$result['total']}}</h4>
              </div>
          </div>
          @if($result['success'] < $result['total'])
            <form action="/examinations/{{$textbook_id}}/{{$chapter_id}}?retry=1" method="POST" autocomplete="off">
              @csrf
              <button type="button" class="btn btn-submit btn-block btn-info btn-lg">まちがえた問題のみ解く</button>
            </form>
          @else
            <a class="btn btn-block btn-secondary btn-lg" href="/examinations/{{$textbook_id}}">章選択に戻る</a>
          @endif
        @else
          <a class="btn btn-block btn-info btn-lg" href="/examinations/{{$textbook_id}}/{{$chapter_id}}">次の問題</a>
        @endif
      @else
      <a class="btn btn-block btn-info btn-lg" href="/examinations/{{$textbook_id}}/{{$chapter_id}}">同じ問題を練習する</a>
      @endif
    </div>
  </div>
</div>
<script>
</script>
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    @component('components.search_word', ['search_word' => $search_word])
    @endcomponent
  </li>
</ul>
@endsection

@section('page_footer')
@endsection
