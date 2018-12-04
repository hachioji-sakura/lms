<html>
<head>
<title>回答結果ページ</title>
<style>
body {
  background: #00ff00;
  font-family: Meiryo;
  font-size: xx-large;
}
input {
  font-size : 30px;
}
</style>

</head>
<body>
{{-- 直近回答の表示+次へのリンク --}}
@if(isset($item))
<!-- h1>{{$chapter_title}} 問題:{{$item['sort_no']}}</h1 -->
<!-- h3>{{$item['title']}}</h3 -->
<h3>問題：<span style="color:#f00;">{{$item['body']}}</span></h3>
<h3>回答内容：<span style="color:#f00;">{{$answer_text}}</span></h3>
  @if($judge===1)
    <h4>
      @if($is_traning===1)
        練習モード：
      @endif
      正解です！
    </h4>
    @if(!isset($result))
      <input type="button" onCLick='location.href="/examinations/{{$textbook_id}}/{{$chapter_id}}";' value="次の問題に進む"><br>
    @endif
  @else
    <h4>
      不正解です。正解は<br>
      <span style="color:#f00;">{{$item['answer_text']}}</span><br>
      です。
    </h4>
    <input type="button" onCLick='location.href="/examinations/{{$textbook_id}}/{{$chapter_id}}";' value="もう１回同じ問題を練習する"><br>
  @endif
@endif

{{-- 結果の表示 --}}
@if(isset($result))
<h1>{{$chapter_title}} はすべて終了しました</h1>
<h4 style="color:#f00;">結果・正解数/問題数：{{$result['success']}} / {{$result['total']}}</h4>
  @if($result['success']!==$result['total'])
  <form action="/examinations/{{$textbook_id}}/{{$chapter_id}}?retry=1" method="POST" autocomplete="off">
    @csrf
    <input type="submit" value="間違った問題のみ再挑戦する"><br>
  </form>
  @endif
@endif
<br>
<br>
<a href="/examinations/{{$textbook_id}}/">章の選択画面へ</a><br>
<a href="/logout">ログイン画面へ</a><br>

</body>
</html>
