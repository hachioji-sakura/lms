<?php
$checked = '';
?>
@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('list_pager')
@endsection

@section('contents')
<div class="row">
  <div class="col-12">
    <div class="card" id="matching_form">
      <form method="POST" action='/events/{{$event_id}}/lesson_requests/matching' >
      @csrf
      @method('PUT')
      <div class="card-header">
        <h3 class="card-title" >
          <i class="fa fa-calendar mr-1"></i>
          講習申し込み一覧
        </h3>
        <div class="card-title text-sm">
          @if($event->is_season_lesson()==true)
          <a class="btn btn-sm btn-outline-success" href="/events/{{$event->id}}/schedules" >
            <i class="fa fa-calendar mr-1"></i>
            予定一覧
          </a>
          <button type="button" class="btn btn-sm btn-submit btn-primary" accesskey="matching_form" confirm="マッチング処理を開始しますか？">
            <i class="fa fa-check"></i>
            マッチング処理開始
          </button>
          @endif
          @yield('list_pager')
        </div>
      </div>
      <div class="card-body table-responsive p-3">
        @if(count($items) > 0)
          @foreach($items as $item)
          <div class="row p-1 bd-gray hr-1">
            <div class="col-8 mt-1">
              <input class="form-check-input icheck flat-red mr-2" type="checkbox" name="selected_lesson_request_ids[]" value="{{$item->id}}"
              <
              />
              <a href="/events/{{$event->id}}/schedules?lesson_request_id={{$item->id}}&search_status=fix">
              <span class="text-xs ml-1">
                <small class="badge badge-{{config('status_style')[$item->status]}} p-1 mr-1">
                  {{$item->status_name()}}
                </small>
                <i class="fa fa-user mr-1"></i>
                {{$item->student->name}} 様
                （{{$item->student->grade}}）<br>
              </span>
              </a>
            </div>
            <div class="col-4 mt-1">
              <span class="text-sm float-right">申込日:{{$item->dateweek_format($item->created_at)}}</span>
            </div>
            <div class="col-12 mt-1 pl-5">
              @foreach($item->charge_subject_attributes() as $attribute)
              @if($item->get_tag_value($attribute->attribute_value.'_day_count')<1)
                @continue
              @else
              {{$attribute->attribute_name}}:{{$item->get_tag_value($attribute->attribute_value.'_day_count')}}
              @endif
              @endforeach
              <br>
              @foreach($item->get_tags('lesson_place') as $tag)
              <span class="text-xs">
                <small class="badge badge-success p-1 mr-1">
                  <i class="fa fa-map-marker mr-1"></i>
                  {{$tag->name()}}
                </small>
              </span>
              @endforeach
            </div>
            <div class="col-12 mt-1 pl-5">
              <span class="mr-1">
                登録済予定数：{{count($item->calendars)}}
                (候補予定数：{{$item->lesson_request_calendar_count(['fix', 'complete'])}})
              </span>
            </div>
            <div class="col-12 text-sm">
              @component('lesson_requests.season_lesson.forms.list_buttons', ['item' => $item, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes]) @endcomponent
            </div>
          </div>
          @endforeach
        @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
        @endif
      </div>
      @if($event->is_season_lesson()==true)
      <div class="card-footer">
        <div class="row">
          <div class="col-12 mb-2 bg-warning p-4">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            選択した申し込みに対しマッチング処理を行い、予定を作成します。<br>
            この処理はしばらく時間がかかります。
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-submit btn-primary w-100" accesskey="matching_form" confirm="マッチング処理を開始しますか？">
              <i class="fa fa-check"></i>
              マッチング処理開始
            </button>
          </div>
        </div>
      </div>
      <script>
      $(function(){
        base.pageSettinged("matching_form", null);
        $("#matching_form button.btn-submit").on('click', function(e){
          e.preventDefault();
          var _confirm = $(this).attr("confirm");
          if(!util.isEmpty(_confirm)){
            if(!confirm(_confirm)) return false;
          }
          if(front.validateFormValue('matching_form')){
            $("#matching_form form").submit();
          }
        });
      });
      function important_checked(){
        var ret = $('input[name="important_check"]').prop('checked');
        $("button.btn-submit").prop("disabled",!ret);
      }
      </script>
      @endif
      </form>
    </div>
  </div>
</div>
@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
    <div class="w-100">
      <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
        @foreach(config('attribute.trial_status') as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['calendar_filter']['search_status']) && in_array($index, $filter['calendar_filter']['search_status'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="form-group">
      <label for="is_desc_1" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" id="is_desc_1" class="icheck flat-green"
      @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  @endslot
@endcomponent
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-envelope-square"></i>
      <p>
        申し込み一覧
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/events/{{$event->id}}/lesson_requests?list=new" class="nav-link @if($list=="new") active @endif">
          <i class="fa fa-exclamation-triangle nav-icon"></i>
          <p>
            未対応
            @if($new_count > 0)
            <span class="badge badge-danger right">{{$new_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/events/{{$event->id}}/lesson_requests?list=fix" class="nav-link @if($list=="fix") active @endif">
          <i class="fa fa-calendar-plus nav-icon"></i>
          <p>
            予定確定
            @if($fix_count > 0)
            <span class="badge badge-primary right">{{$fix_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/events/{{$event->id}}/lesson_requests?list=cancel" class="nav-link @if($list=="cancel") active @endif">
          <i class="fa fa-calendar-plus nav-icon"></i>
          <p>
            申込キャンセル
            @if($cancel_count > 0)
            <span class="badge badge-secondary right">{{$cancel_count}}</span>
            @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        その他
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link @if(empty($_status)) active @endif">
          <i class="fa fa-history nav-icon"></i>
          <p>
            履歴
          </p>
        </a>
      </li>
      {{--
      <li class="nav-item">
        <a href="/{{$domain}}" class="nav-link">
          <i class="fa fa-list-alt nav-icon"></i>すべて
        </a>
      </li>
      --}}
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
{{--
  <dt>
    <a class="btn btn-app"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="">
      <i class="fa fa-plus"></i>{{$domain_name}}登録
    </a>
  </dt>
--}}
@endsection
