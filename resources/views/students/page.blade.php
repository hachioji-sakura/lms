@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@include('dashboard.widget.milestones')

@include('dashboard.widget.star_comments')

{{--まだ対応しない
@include('dashboard.widget.events')
@include('dashboard.widget.tasks')
--}}

@section('contents')
<section id="member" class="content-header">
	<div class="row">
		<div class="col-md-4 mb-2">
      @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
          @slot('courtesy')
          　様
          @endslot
          @slot('alias')
            <h6 class="widget-user-desc">
              <small class="badge badge-dark mt-1 mr-1">
                No.{{$item->tag_name('student_no')}}
              </small>
              <small class="badge badge-{{config('status_style')[$item->status]}} mt-1 mr-1">
                {{$item->status_name()}}
              </small>
              @if($item->is_juken()==true)
              <small class="badge badge-warning mt-1 mr-1" title="">
                受験生
              </small>
              @endif
              @if($item->is_fee_free()==true)
              <small class="badge badge-secondary mt-1 mr-1" title="">
                受講料無料
              </small>
              @endif
              @if($item->is_arrowre()==true)
              <small class="badge badge-secondary mt-1 mr-1" title="">
                アローレ所属
              </small>
              @endif
              <small class="badge badge-primary mt-1 mr-1">
                {{$item->label_gender()}}
              </small>
              <small class="badge badge-primary mt-1 mr-1">
                {{$item->grade()}}
              </small>
              @if(!empty($item->school_name()))
              <small class="badge badge-primary mt-1 mr-1">
                {{$item->school_name()}}
              </small>
              @endif
              @foreach($item->user->tags as $tag)
              @if($user->role==="manager" && $tag->tag_key=="student_character")
                <small class="badge badge-info mt-1 mr-1">
                  {{$tag->name()}}
                </small>
              @endif
              @endforeach
            </h6>
            {{--
            <div class="card-footer p-0">
              <ul class="nav flex-column">
                @if(!empty($item->recess_duration()))
                <li class="nav-item pl-1">
                  <a href="/{{$domain}}/{{$item->id}}/recess" class="nav-link">
                    <i class="fa fa-calendar-alt mr-2"></i>
                    休会予定：{{$item->recess_duration()}}
                  </a>
                </li>
                @endif
                @if(!empty($item->unsubscribe_date_label()))
                <li class="nav-item pl-1">
                  <a href="/{{$domain}}/{{$item->id}}/unsubscribe" class="nav-link">
                    <i class="fa fa-calendar-alt mr-2"></i>
                    退会予定：{{$item->unsubscribe_date_label()}}
                  </a>
                </li>
                @endif
                <li class="nav-item">
                  <a href="/{{$domain}}/{{$item->id}}/schedule?list=month" class="nav-link @if($view=="schedule" && $list=="month") active @endif">
                    <i class="fa fa-calendar-check mr-2"></i>
                      {{__('labels.month_schedule_list')}}
                      @if($month_count > 0)
                      <span class="badge badge-primary float-right">{{$month_count}}</span>
                      @endif
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/{{$domain}}/{{$item->id}}/schedule?list=confirm" class="nav-link  @if($view=="schedule" && $list=="confirm") active @endif">
                  <i class="fa fa-hourglass mr-2"></i>
                    {{__('labels.adjust_schedule_list')}}
                    @if($confirm_count > 0)
                    <span class="badge badge-warning float-right">{{$confirm_count}}</span>
                    @endif
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/{{$domain}}/{{$item->id}}/schedule?list=rest_contact" class="nav-link  @if($view=="schedule" && $list=="rest_contact") active @endif">
                  <i class="fa fa-calendar-times mr-2"></i>
                    {{__('labels.rest_contact')}}
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/{{$domain}}/{{$item->id}}/schedule?list=exchange" class="nav-link @if($view=="schedule" && $list=="exchange") active @endif">
                    <i class="fa fa-exchange-alt mr-2"></i>
                    {{__('labels.exchange_schedule_list')}}
                    @if($exchange_count > 0)
                    <span class="badge badge-danger float-right">{{$exchange_count}}</span>
                    @endif
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{__('labels.students')}}{{__('labels.setting')}}">
                    <i class="fa fa-user-edit mr-2"></i>{{__('labels.students')}}{{__('labels.setting')}}
                  </a>
                </li>
              </ul>
            </div>
            --}}
            {{--
            <li class="nav-item">
              <a href="/examinations" class="nav-link active">
                <i class="fa fa-file-signature mr-2"></i>
                確認テスト
                <span class="float-right badge bg-danger">New</span>
              </a>
            </li>
            --}}

          @endslot
      @endcomponent
		</div>
		<div class="col-md-8">
      @yield('milestones')
		</div>
	</div>
</section>

<section class="content-header">
	<div class="container-fluid">
    <div class="row">
      @yield('star_comments')
      @yield('comments')
    </div>
	</div>
</section>

@endsection
