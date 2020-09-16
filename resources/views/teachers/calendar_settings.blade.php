@section('title')
  {{$domain_name}} {{__('labels.regular_schedule_list')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content mb-2">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-clock mr-1"></i>
            @if($domain=='teachers')
            {{__('labels.regular_schedule_list')}}
            @else
            シフト一覧
            @endif
          </h3>
          <div class="card-tools">
            @if($user->role=="manager")
            <a class="btn btn-primary btn-sm float-left mr-1" href="javascript:void(0);" page_title="{{__('labels.setting')}}{{__('labels.add')}}" page_form="dialog"
            @if($domain=="teachers")
              page_url="/calendar_settings/create?teacher_id={{$item->id}}"
            @elseif($domain=="managers")
              page_url="/calendar_settings/create?manager_id={{$item->id}}"
            @endif
             role="button">
              <i class="fa fa-plus"></i>
              <span class="btn-label">{{__('labels.setting')}}{{__('labels.add')}}</span>
            </a>
              {{--
              <a class="btn btn-outline-success btn-sm float-left mr-1" href="javascript:void(0);" page_title="{{__('labels.repeat_schedule_add')}}" page_form="dialog"
              @if($domain=="teachers")
                page_url="/calendar_settings/all_to_calendar?teacher_id={{$item->id}}"
              @elseif($domain=="managers")
                page_url="/calendar_settings/all_to_calendar?manager_id={{$item->id}}"
              @endif
               role="button">
                <i class="fa fa-calendar-plus"></i>
                <span class="btn-label">{{__('labels.repeat_schedule_add')}}</span>
              </a>
              --}}
            @endif

            <a class="btn btn-default btn-sm float-left" data-toggle="modal" data-target="#filter_form">
              <i class="fa fa-filter"></i>
              <span class="btn-label">{{__('labels.filter')}}</span>
            </a>
          </div>
        </div>
        <div class="card-body p-0">
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
                    {{$setting->lesson_week()}}
                  </a>
                </div>
              </div>
              <div id="{{$setting["lesson_week"]}}" class="collapse show">
            @endif

            <div class="row p-2 border-bottom
              @if($setting['status']=='disabled' || $setting->has_enable_member()==false)
              calendar_rest
              @endif
              ">

              <input type="hidden" name="setting_id[]" value="{{$setting['id']}}" >
              <div class="col-12 col-md-4">
                <a href="javascript:void(0);" title="{{$setting->id}}" page_title="{{__('labels.details')}}" page_form="dialog" page_url="/calendar_settings/{{$setting->id}}" role="button" class="">
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
                <small title="{{$setting->id}}" class="ml-1 badge badge-{{config('status_style')[$setting['status']]}} mt-1 mr-1">{{$setting['status_name']}}</small>
              </div>
              <div class="col-12 col-md-8">
                <span class="mr-2">
                  @if($setting->is_teaching()==true)
                    {{$setting['course']}} /  {{$setting['course_minutes_name']}}
                  @else
                    {{$setting['work_name']}}
                  @endif
                </span>
                @if($setting->is_online()==true)
                <small class="badge badge-info mr-1 text-sm">
                  <i class="fa fa-globe">{{__('labels.online')}}</i>
                </small>
                @endif
              </div>
              <div class="col-12 text-sm">
                  設定有効期間：{{$setting->enable_date()}}
              </div>
              <div class="col-12">
                @if($setting->work!=9)
                  @if($setting->has_enable_member()==false)
                  <small class="ml-1 mr-1 text-sm text-danger">{{__('messages.error_user_calendar_settings_no_member')}}</small>
                  @endif
                  @foreach($setting->members as $member)
                    @if($member->user->details()->role==="student")
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
                @endif
              </div>
              <div class="col-12 col-md-6 mt-1">
                <a href="/{{$domain}}/{{$item['id']}}/schedule?list=history&user_calendar_setting_id={{$setting->id}}" class="text-sm">
                  {{__('labels.regist_schedule_count', ['count' => $setting['calendar_count']])}} /    {{__('labels.last_regist_date')}}:
                  @isset($setting['last_schedule'])
                  {{$setting['last_schedule']['date']}}
                  @endisset
                </a>
              </div>
              <div class="col-12 col-md-6 text-right mt-1">
                @component('teachers.forms.calendar_setting_button', ['setting' => $setting, 'user' => $user])
                @endcomponent
              </div>
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
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
          @endif
        </div>
      {{--
        <!-- /.card-body -->
        <div class="card-footer clearfix">
          <button type="button" class="btn btn-info btn-sm float-left">
            <i class="fa fa-plus mr-2"></i>{{__('labels.add')}}
          </button>
        </div>
        --}}
      </div>
      <!-- /.card -->
    </div>
  </div>
</section>


@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  @component('calendar_settings.filter', ['domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'filter'=>$filter])
  @endcomponent
  @endslot
@endcomponent

@endsection
