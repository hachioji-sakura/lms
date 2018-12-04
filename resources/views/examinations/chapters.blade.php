<html>
<head>
<title>章を選択</title>
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
<table>
  <tr>
    <th>章名</th>
  </tr>
  @foreach($items as $item)
  <tr>
    <td>
      <a href="/examinations/{{$item['textbook_id']}}/{{$item['id']}}">{{$item['title']}}</a>
    </td>
  </tr>
  @endforeach
</body>
</html>
