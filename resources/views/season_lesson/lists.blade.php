@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('list_pager')
  @if(count($items) > 0)
  {{$items->appends(Request::query())->links('components.paginate')}}
  @endif
@endsection

@section('contents')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title" id="charge_students">
          <i class="fa fa-calendar mr-1"></i>
          講習申し込み一覧
        </h3>
        <div class="card-title text-sm">
          <a class="btn btn-sm btn-primary" href="javascript:void(0);" page_url="/events/{{$event_id}}/lesson_requests/matching" page_title="予定マッチング" page_form="dialog" >
            <i class="fa fa-hands-helping mr-1"></i>
            予定マッチング
          </a>
          @yield('list_pager')
        </div>
      </div>
      <div id="season_lesson_list" class="card-body table-responsive p-3">
        @if(count($items) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($items as $item)
            <li class="col-12" accesskey="" target="">
                <div class="row">
                  <div class="col-12 col-lg-4 col-md-6 mt-1">
                    <a href="season_lessons/{{$item->id}}">
                    <span class="text-xs">
                      <small class="badge badge-{{config('status_style')[$item->status]}} p-1 mr-1">
                        {{$item->status_name()}}
                      </small>
                    </span>
                    <span class="text-sm time">申込日:{{$item->dateweek_format($item->created_at)}}</span>
                    <br>
                    <span class="text-xs ml-1">
                      <i class="fa fa-user mr-1"></i>
                      {{$item->student->name}} 様
                      （{{$item->student->grade}}）<br>
                    </small>
                    </a>
                  </div>
                  <div class="col-12 col-lg-4 col-md-6 mt-1 text-sm">
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
                  <div class="col-12 col-lg-4 mt-1 text-sm">
                    @component('season_lesson.forms.list_buttons', ['item' => $item, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes]) @endcomponent
                  </div>
                </div>
            </li>
            @endforeach
          </ul>
        @else
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
        </div>
        @endif
      </div>
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
        <a href="/events/{{$event_id}}/lesson_requests?list=new" class="nav-link @if($list=="new") active @endif">
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
        <a href="/events/{{$event_id}}/lesson_requests?list=fix" class="nav-link @if($list=="fix") active @endif">
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
        <a href="/events/{{$event_id}}/lesson_requests?list=cancel" class="nav-link @if($list=="cancel") active @endif">
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
