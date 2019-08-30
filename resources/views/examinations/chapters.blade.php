@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('contents')
<div class="card-header">
  <div class="card-tools">
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
    <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
  </div>
  @endif
</div>
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item hr-1">
        @component('components.search_word', ['search_word' => $search_word])
        @endcomponent
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
@endsection
