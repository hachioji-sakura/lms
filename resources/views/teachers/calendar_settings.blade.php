@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

{{--まだ対応しない
@include('dashboard.widget.milestones')
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

@section('contents')
<section class="content mb-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-clock mr-1"></i>
            通常授業
          </h3>
          <div class="card-tools">
            <a class="btn btn-default btn-sm mr-2 float-right" role="button" data-toggle="collapse" data-parent="#filter_form" href="#filter_form_item" class="" aria-expanded="true">
              <i class="fa fa-filter mr-1"></i>絞込
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          @component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
            @slot("search_form")
            <div class="col-12 col-lg-4 col-md-6">
              <label for="charge_subject" class="w-100">
                曜日
              </label>
              <div class="w-100">
                <select name="search_week[]" class="form-control select2" width=100% placeholder="検索曜日" multiple="multiple" >
                  @foreach($attributes['lesson_week'] as $index=>$name)
                    <option value="{{$index}}"
                    @if(isset($filter['search_week']) && in_array($index, $filter['search_week'])==true)
                    selected
                    @endif
                    >{{$name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-lg-4 col-md-6">
              <label for="charge_subject" class="w-100">
                作業
              </label>
              <div class="w-100">
                <select name="search_work[]" class="form-control select2" width=100% placeholder="検索作業" multiple="multiple" >
                  @foreach($attributes['work'] as $index=>$name)
                    <option value="{{$index}}"
                    @if(isset($filter['search_work']) && in_array($index, $filter['search_work'])==true)
                    selected
                    @endif
                    >{{$name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-lg-4 col-md-6">
              <label for="charge_subject" class="w-100">
                場所
              </label>
              <div class="w-100">
                <select name="search_place[]" class="form-control select2" width=100% placeholder="検索場所" multiple="multiple" >
                  @foreach($attributes['places'] as $place)
                    @foreach($place->floors as $floor)
                    <option value="{{$floor->id}}"
                    @if(isset($filter['search_place']) && in_array($floor->id, $filter['search_place'])==true)
                    selected
                    @endif
                    >{{$floor->name}}</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
            @endslot
          @endcomponent

          @if(count($calendar_settings) > 0)
          <?php
            $__week = "";
          ?>
          <ul class="mailbox-attachments clearfix row">
            @foreach($calendar_settings as $setting)
            @if($__week != $setting["lesson_week"])
            <li class="col-12 p-0" accesskey="" target="">
              <div class="row">
                <div class="col-12">
                  <a data-toggle="collapse" data-parent="#month_work_list" href="#{{$setting["lesson_week"]}}" class="ml-2 my-1" aria-expanded="false">
                    <i class="fa fa-chevron-down mr-1"></i>
                    {{$setting->lesson_week()}}曜日
                  </a>
                </div>
              </div>
              <div id="{{$setting["lesson_week"]}}" class="collapse show">
            @endif

            <div class="row pl-3 p-1 border-bottom">
              <input type="hidden" name="setting_id[]" value="{{$setting['id']}}" >
              <div class="col-12 col-lg-4 col-md-4">
                <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}" role="button" class="">
                  @if($setting->schedule_method=="month")
                    <span class="text-xs mr-2">
                      <small class="badge badge-info mt-1 mr-1">
                        {{$setting["week_setting"]}}
                      </small>
                    </span>
                  @endif
                  <span class="mr-2">
                    <i class="fa fa-clock"></i>{{$setting["timezone"]}}
                  </span>
                  <span class="mr-2">
                    <i class="fa fa-map-marker"></i>{{$setting["place_floor_name"]}}
                  </span>
                </a>
              </div>
              <div class="col-12 col-lg-6 col-md-6">
                @foreach($setting->members as $member)
                  @if($member->user->details()->role==="student")
                  {{--
                    <a href="/students/{{$member->user->details()->id}}">
                      <i class="fa fa-user-graduate"></i>
                      {{$member->user->details()->name}}
                    </a>
                    --}}
                    <span class="mr-2">
                    <i class="fa fa-user-graduate"></i>
                    {{$member->user->details()->name}}
                    </span>
                  @endif
                @endforeach
                @foreach($setting['subject'] as $subject)
                <span class="text-xs mr-2">
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$subject}}
                  </small>
                </span>
                @endforeach
              </div>
              {{--
              <div class="col-12 col-lg-2 col-md-2">
                <a href="javascript:void(0);" title="{{$setting["id"]}}" page_title="詳細" page_form="dialog" page_url="/calendar_settings/{{$setting["id"]}}" role="button" class="btn btn-default btn-sm float-left mr-1 mt-1 float-right">
                  <i class="fa fa-edit"></i>変更
                </a>
              </div>
              --}}
            </div>
            <?php
              $__week = $setting["lesson_week"];
            ?>
            @if($__week != $setting["lesson_week"])
              </div>
            </li>
            @endif
          @endforeach
          </ul>
          @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
          </div>
          @endif
        </div>
      {{--
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <button type="button" class="btn btn-info btn-sm float-left">
            <i class="fa fa-plus mr-2"></i>追加
          </button>
        </div>
        --}}
      </div>
      <!-- /.card -->
    </div>
  </div>
</section>

{{--まだ対応しない
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-md-6">
				@yield('milestones')
			</div>
			<div class="col-12 col-lg-6 col-md-6">
				@yield('events')
			</div>
		</div>
	</div>
</section>

<section class="content">
	@yield('tasks')
</section>
--}}
@endsection
