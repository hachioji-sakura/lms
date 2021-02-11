@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('teachers.page')
@include($domain.'.menu')

@section('sub_contents')
<div class="row">
  @foreach($text_materials as $text_material)
  <div class="bg-info col-12 col-lg-4 col-md-6 mb-4">
    {{$text_material}}
  </div>
  @endforeach
</div>
@endsection
