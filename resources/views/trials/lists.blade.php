@section('title')
  {{$domain_name}}一覧
@endsection
@extends('dashboard.common')

@section('list_pager')
@component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
  @slot("addon_button")
  @endslot
@endcomponent
@endsection

@section('contents')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title" id="charge_students">
          <i class="fa fa-calendar mr-1"></i>
          体験申し込み一覧
        </h3>
        <div class="card-title text-sm">
          @yield('list_pager')
        </div>
      </div>
      <div id="trial_list" class="card-body table-responsive p-3">
        @if(count($items) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($items as $item)
            <?php
              $get_tagdata = $item->get_tagdata();
              $tagdata = $get_tagdata["tagdata"];
              $status = $item->get_status();
             ?>
            <li class="col-12" accesskey="" target="">
                <div class="row">
                  <div class="col-12 col-lg-4 col-md-6 mt-1">
                    <a href="trials/{{$item->id}}">
                    <span class="text-xs">
                      <small class="badge badge-{{config('status_style')[$status]}} p-1 mr-1">
                        {{$item->status_name()}}
                      </small>
                    </span>
                    <span class="text-sm time">申込日:{{$item->dateweek_format($item->created_at)}}</span>
                    <br>
                    <span class="text-xs ml-1">
                      <i class="fa fa-user mr-1"></i>
                      {{$item->student->name()}} 様
                      （{{$item->student->grade()}}）<br>
                    </small>
                    </a>
                    @isset($tagdata["lesson"])
                    @foreach($tagdata["lesson"] as $label)
                    <span class="text-xs">
                      <small class="badge badge-primary p-1 mr-1">
                        <i class="fa fa-chalkboard mr-1"></i>
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                    @endisset
                    @isset($tagdata["lesson_place"])
                    @foreach($tagdata["lesson_place"] as $label)
                    <span class="text-xs">
                      <small class="badge badge-success p-1 mr-1">
                        <i class="fa fa-map-marker mr-1"></i>
                        {{$label}}
                      </small>
                    </span>
                    @endforeach
                    @endisset
                  </div>
                  <div class="col-12 col-lg-4 col-md-6 mt-1 text-sm">
                    @if($item->is_trial_lesson_complete()==false)
                      第1希望:{{$item->trial_start_end_time(1)}}</span><br>
                      第2希望:{{$item->trial_start_end_time(2)}}</span><br>
                      第3希望:{{$item->trial_start_end_time(3)}}</span>
{{--
<br>
第4希望:{{$item->trial_start_end_time(4)}}</span><br>
第5希望:{{$item->trial_start_end_time(5)}}</span>
--}}
                    @else
                      入会希望連絡: {{$item->entry_contact_send_date()}}</span><br>
                      授業開始希望日: {{$item->dateweek_format($item->schedule_start_hope_date)}}</span><br>
                      入会案内連絡: {{$item->entry_guidanced_send_date()}}</span><br>
                    @endif
                  </div>
                  <div class="col-12 col-lg-4 mt-1 text-sm">
                    @component('trials.forms.trial_button', ['item' => $item, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes]) @endcomponent
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
        体験授業完了前
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}?list=new" class="nav-link @if($list=="new") active @endif">
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
        <a href="/{{$domain}}?list=confirm" class="nav-link @if($list=="confirm") active @endif">
          <i class="fa fa-calendar-alt nav-icon"></i>
          <p>
            体験授業調整中
            @if($confirm_count > 0)
            <span class="badge badge-warning right">{{$confirm_count}}</span>
            @endif
          </p>
        </a>
      </li>
      {{-- TODO : 実用化されるまでコメントアウト
      <li class="nav-item">
        <a href="/{{$domain}}?list=reapply" class="nav-link @if($list=="reapply") active @endif">
          <i class="fa fa-calendar-alt nav-icon"></i>
          <p>
            希望日変更依頼中
            @if($reapply_count > 0)
            <span class="badge badge-secondary right">{{$reapply_count}}</span>
            @endif
          </p>
        </a>
      </li>
      --}}
      <li class="nav-item">
        <a href="/{{$domain}}?list=fix" class="nav-link @if($list=="fix") active @endif">
          <i class="fa fa-calendar-plus nav-icon"></i>
          <p>
            体験授業確定
            @if($fix_count > 0)
            <span class="badge badge-primary right">{{$fix_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?list=presence" class="nav-link @if($list=="presence") active @endif">
          <i class="fa fa-calendar-check nav-icon"></i>
          <p>
            体験授業完了
            @if($presence_count > 0)
            <span class="badge badge-success right">{{$presence_count}}</span>
            @endif
          </p>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-envelope-open-text"></i>
      <p>
        体験授業完了後
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/{{$domain}}?list=entry_contact" class="nav-link @if($list=="entry_contact") active @endif">
          <i class="fa fa-hourglass-half nav-icon"></i>
          <p>
            入会希望連絡待ち
            @if($entry_contact_count > 0)
            <span class="badge badge-secondary right">{{$entry_contact_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?list=entry_hope" class="nav-link @if($list=="entry_hope") active @endif">
          <i class="fa fa-thumbs-up nav-icon"></i>
          <p>
            入会希望あり
            @if($entry_hope_count > 0)
            <span class="badge badge-danger right">{{$entry_hope_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?list=entry_guidanced" class="nav-link @if($list=="entry_guidanced") active @endif">
          <i class="fa fa-file-export nav-icon"></i>
          <p>
            入会案内連絡済
            @if($entry_guidanced_count > 0)
            <span class="badge badge-secondary right">{{$entry_guidanced_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?list=complete" class="nav-link @if($list=="complete") active @endif">
          <i class="fa fa-check-circle nav-icon"></i>
          <p>
            入会済み
            @if($complete_count > 0)
            <span class="badge badge-secondary right">{{$complete_count}}</span>
            @endif
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/{{$domain}}?list=entry_cancel" class="nav-link @if($list=="entry_cancel") active @endif">
          <i class="fa fa-ban nav-icon"></i>
          <p>
            入会キャンセル
            @if($entry_cancel_count > 0)
            <span class="badge badge-secondary right">{{$entry_cancel_count}}</span>
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
