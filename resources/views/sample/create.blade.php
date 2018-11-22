<html>
<head>
<title>画像アップロード</title>
</head>
<body>
<h1>画像アップロード</h1>

{{--成功時のメッセージ--}}
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
{{-- エラーメッセージ --}}
@if ($errors->any())
    <div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
@endif
<form method="post" action="/images" enctype="multipart/form-data">
  @csrf
  alias:<input type="text" name="alias"><br>
  <div class="form-row">
    <div class="form-group col-12">
      <label class="col-sm-3 control-label" for="image">画像アップロード</label>
      <input type="file" name="image" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" placeholder="ファイル">
      @if ($errors->has('image'))
      <span class="invalid-feedback">
      <strong>{{ $errors->first('image') }}</strong>
      </span>
      @endif
    </div>
  </div>
  <input type="submit" value="upload">
<form>


</body>
</html>
