<html>
<head>
<title>問題集を選択</title>
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
    <th>名称</th>
  </tr>
  @foreach($items as $item)
  <tr>
    <td>
      <a href="/examinations/{{$item['id']}}">{{$item['name']}}</a>
    </td>
  </tr>
  @endforeach
</body>
</html>
