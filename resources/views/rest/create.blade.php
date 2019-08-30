<html>
<head>
<title>REST_FORM</title>
</head>
<body>
@if(isset($form))
<h1>PUTを送信するフォーム</h1>
<form action="/rest/{{$form->id}}" method="POST">
{{ csrf_field() }}

<input type="hidden" name="_method" value="PUT">
NAME:<input type="text" name="name" value="{{$form->name}}"><br>
MESSAGE:<input type="text" name="message" value="{{$form->message}}"><br>
<input type="submit" value="send">
</form>
<hr>

<h1>DELETEを送信するフォーム</h1>
<form action="/rest/{{$form->id}}" method="POST">
{{ csrf_field() }}
<h5>NAME:{{$form->name}}</h5>
 <h5>MESSAGE :{{$form->message}}</h5>
 <input type="hidden" name="_method" value="DELETE">
 <input type="submit" value="send">
</form>


@else
<h1>POSTを送信するフォーム</h1>
<form action="/rest" method="POST">
{{ csrf_field() }}
NAME:<input type="text" name="name"><br>
MESSAGE:<input type="text" name="message"><br>
<input type="submit" value="send">
</form>

<hr>
@endif

</body>
</html>
