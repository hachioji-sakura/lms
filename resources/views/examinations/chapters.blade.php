@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('contents')
<div class="card-header">
  <div class="card-tools">
    <div class="input-group input-group-sm" style="">
      <input type="text" name="search_word" class="form-control float-right stretch" placeholder="Search" value="{{$search_word}}">
      <div class="input-group-append">
        <button type="submit" class="btn btn-default" id="search_button">
          <i class="fa fa-search"></i>
        </button>
      </div>
      <!--
      <a type="button" class="btn btn-primary btn-sm" href="#">
        <i class="fa fa-plus"></i>
        <span class="btn-label">追加</span>
      </a>
      -->
    </div>
  </div>
  <h3 class="card-title">
    <a href="/examinations">{{str_limit($textbook_title, 42,'...')}}</a> ＞ 章を選択してください
  </h3>
</div>
<div class="card-body table-responsive p-4">
  @if(count($items) > 0)
    @foreach($items as $item)
    <div class="row mb-4">
      <div class="col-11">
        <a class="" href="/examinations/{{$item['textbook_id']}}/{{$item['id']}}">
          @if($item['examination_status'] == 10)
            <div class="callout callout-success text-dark">
          @elseif($item['examination_status'] == 1)
            <div class="callout callout-danger text-dark">
          @else
            <div class="callout callout-secondary text-dark">
          @endif
          <span class="float-right mt-1 text-secondary" style="font-size:2rem;"><i class="fa fa-chevron-right mr-2"></i></span>
          <h5>{{$item['title']}}</h5>
          <span>
            問題数:{{$item['question_count']}} /
            @if($item['examination_count'] == 0)
              未実施
            @else
              実施数：{{$item['examination_count']}}回
            @endif
            @if($item['examination_status'] == 10)
              / 完了
            @elseif($item['examination_status'] == 1)
              / 回答中
            @else
              / 未回答
            @endif
          </span>
        </div>
        </a>
      </div>
    </div>
    @endforeach
  @else
  <div class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
  @endif
</div>
@endsection

@section('page_sidemenu')
@endsection

@section('page_footer')
@endsection
