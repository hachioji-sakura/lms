@section('title')
  {{$domain_name}}依頼一覧
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            {{$list_title}}
          </h3>
          <div class="card-title text-sm">
            @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count]) @endcomponent
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($asks) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($asks as $ask)
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-8 mt-1">
                  <a href="javascript:void(0);" title="{{$ask["id"]}}" page_title="詳細" page_form="dialog" page_url="/asks/{{$ask["id"]}}" >
                    <i class="fa fa-envelope-square mx-1"></i>{{$ask["type_name"]}}<br>
                  </a>
                </div>
                <div class="col-6 mt-1 text-sm">
                  依頼者：{{$ask["target_user_name"]}}
                </div>
                <div class="col-6 mt-1 text-sm">
                  担当者： {{$ask["charge_user_name"]}}
                </div>
                <div class="col-6 mt-1 text-sm">
                  期限：{{$ask["end_dateweek"]}}
                </div>
                <div class="col-12 text-sm mt-1 text-right">
                  @component('teachers.forms.ask_button', ['teacher'=>$item, 'ask' => $ask, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
                  @endcomponent
                </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")

  @endslot
@endcomponent
@endsection
