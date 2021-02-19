@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('teachers.page')
@include($domain.'.menu')

@section('sub_contents')

<div class=row>
  <div class="col-md-12">
    <a href="javascript:void(0);" page_title="教材登録" page_form="dialog" page_url="/text_materials/create" class="btn btn-block btn-primary btn-lg">教材登録</a>
  </div>
</div>

@foreach($text_materials as $text_material)
  <div class="row">
    <div class="col-12">
      <a href="{{$text_material->s3_url}}" class="btn btn-block btn-primary btn-lg">{{$text_material->name}}</a>
    </div>
    <div class="col-12">
      {{$text_material->description}}
    </div>
  </div>
@endforeach




@endsection
