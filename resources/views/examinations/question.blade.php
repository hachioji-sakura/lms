<html>
<head>
<title>問題ページ</title>
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
<p>
  {{$message}}
</p>
{{-- 問題の表示 --}}
@if(isset($item))
<h3>{{$item['title']}}</h3>
<h3>問題：<span style="color:#f00;">{{$item['body']}}</span></h3>
<form action="/examinations/{{$textbook_id}}/{{$chapter_id}}/{{$item['id']}}" method="POST" autocomplete="off">
@csrf
解答記入欄：<input type="text" id="answer_text" name="answer_text" />
<input type="hidden" size=30 id="start_time" name="start_time" value="{{date('Y-m-d H:i:s')}}"/>
<br>
<input type="submit" value="送信する" />
@endif
@if(isset($result))
<h1>{{$chapter_title}} はすべて終了しました</h1>
<h4 style="color:#f00;">結果・正解数/問題数：{{$result['score']}} / {{$result['total']}}</h4>
<form action="/examinations/{{$textbook_id}}/{{$chapter_id}}?retry=1" method="POST" autocomplete="off">
  @csrf
  <input type="submit" value="間違った問題のみ再挑戦する"><br>
</form>
@endif

<br>
<br>
<a href="/examinations/{{$textbook_id}}/">章の選択画面へ</a><br>
<a href="/logout">ログイン画面へ</a><br>

</body>
</html>
