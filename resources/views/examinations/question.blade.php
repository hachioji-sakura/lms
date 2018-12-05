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
@if(isset($result))
  <h3>{{$chapter_title}} にすべて回答しました</h3>
  <div class="row">
      <div class="col-12">
        <h4 class="text-primary">結果・正解数/問題数：{{$result['success']}} / {{$result['total']}}</h4>
      </div>
  </div>
</div>
<div class="card-body">
  <div class="row">
    <div class="col-6">
    <form action="/examinations/{{$textbook_id}}/{{$chapter_id}}?retry=1" method="POST" autocomplete="off">
      @csrf
      @if($result['success'] < $result['total'])
      <button type="submit" class="btn btn-block btn-info btn-lg">まちがえた問題のみ解く</button>
      @else
      <button type="submit" class="btn btn-block btn-info btn-lg">もう一度問題を解く</button>
      @endif
    </form>
    </div>
    <div class="col-6">
    <a class="btn btn-block btn-info btn-lg" href="/examinations/{{$textbook_id}}">章選択に戻る</a>
    </div>
  </div>
@else
  <h1 class="card-title">No{{$item['sort_no']}}.{{$item['title']}}</h1>
  <div class="row">
    <div class="col-12">
      <h2>{{$item['body']}}</h2>
    </div>
  </div>
</div>
<div class="card-body">
  <form action="/examinations/{{$textbook_id}}/{{$chapter_id}}/{{$item['id']}}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" size=30 id="start_time" name="start_time" value="{{date('Y-m-d H:i:s')}}"/>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <input type="text" id="answer_text" name="answer_text" class="form-control" placeholder="回答を入力してください" required="true" autofocus>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-6">
        <button type="submit" class="btn btn-block btn-info btn-lg">回答する</button>
      </div>
      <div class="col-6">
        <button type="reset" class="btn btn-block btn-secondary btn-lg">クリア</button>
      </div>
    </div>
  </form>
@endif
</div>
<script>
</script>
@endsection

@section('page_sidemenu')
@endsection

@section('page_footer')
@endsection
