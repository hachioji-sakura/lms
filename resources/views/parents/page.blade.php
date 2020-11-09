@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')
@section('contents')
{{--
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/{{$domain}}/{{$item->id}}/ask" class="">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-phone"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">{{__('labels.contact_page')}}</b>
            <span class="text-sm">{{__('labels.contact_page_description')}}</span>
          </div>
        </div>
        </a>
      </div>
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/faqs" class="">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-question"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">{{__('labels.faqs')}}</b>
            <span class="text-sm">{{__('labels.faqs_description')}}</span>
            <b class="info-box-text text-lg"></b>
            <span class="text-sm"></span>
          </div>
        </div>
        </a>
      </div>
    </div>
	</div>
</section>
--}}

<section class="content mb-2">
  <div class="row">
    <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title" id="charge_students">
              <i class="fa fa-users mr-1"></i>
              登録生徒
            </h3>
            <div class="card-title text-sm">
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            @if(count($charge_students) > 0)
            <ul class="mailbox-attachments p-0 clearfix row">
              @foreach($charge_students as $charge_student)
              <li class="col-12 col-lg-6" accesskey="" target="">
                <div class="row">
                  <div class="col-6 text-center">
                    @if($charge_student->student->status!='unsubscribe')
                    <a alt="student_name" href="/students/{{$charge_student->student->id}}">
                    @endif
                      <img src="{{$charge_student->student->user->details()->icon}}" class="img-circle mw-64px w-50">
                      <br>
                      <ruby style="ruby-overhang: none">
                        <rb>{{$charge_student->student->name()}}</rb>
                        <rt>{{$charge_student->student->kana()}}</rt>
                      </ruby>
                    @if($charge_student->student->status!='unsubscribe')
                    </a>
                    @endif
                    <span class="text-xs mx-2">
                      <small class="badge badge-{{config('status_style')[$charge_student->student->status]}} mt-1 mr-1">
                        {{$charge_student->student->status_name()}}
                      </small>
                    </span>
                  </div>
                  <div class="col-6 text-sm">
                    @if(!empty($charge_student->current_calendar()))
                      <a title="{{$charge_student->calendar_id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->current_calendar()->id}}">
                        <i class="fa fa-calendar mr-1"></i>{{$charge_student->current_calendar()->details()->date}}
                        <br>
                        <i class="fa fa-clock mr-1"></i>{{$charge_student->current_calendar()->details()->timezone}}
                      </a>
                      <br>
                      @foreach($charge_student->current_calendar()->details()->subject as $subject)
                      <span class="text-xs mx-2">
                        <small class="badge badge-primary mt-1 mr-1">
                          {{$subject}}
                        </small>
                      </span>
                      @endforeach
                    @else
                    -
                    @endif
                  </div>
                  <div class="col-12 text-sm">
                    @if(!empty($charge_student->current_calendar()) && $user->role==="teacher")
                      @if($charge_student->current_calendar()->details()->status==="fix" && date('Ymd', strtotime($charge_student->current_calendar()->details()->start_time)) === date('Ymd'))
                        <a title="{{$charge_student->current_calendar()->details()->id}}" href="javascript:void(0);" page_title="出欠を取る" page_form="dialog" page_url="/calendars/{{$charge_student->current_calendar()->details()->id}}/presence?origin={{$domain}}&item_id={{$item->id}}&student_id={{$charge_student->student->id}}" role="button" class="btn btn-info btn-sm btn-flat">
                          <i class="fa fa-user-check mr-1"></i>
                          {{$charge_student->current_calendar()->details()->status_name}}
                        </a>
                      @elseif($charge_student->current_calendar()->details()->status==="presence")
                        {{-- 出席済み --}}
                        <a title="{{$charge_student->calendar_id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->calendar_id}}" role="button" class="btn btn-success btn-sm btn-flat">
                          <i class="fa fa-check-circle mr-1"></i>
                          {{$charge_student->current_calendar()->details()->status_name}}
                        </a>
                      @else
                        <a title="{{$charge_student->current_calendar()->details()->id}}" href="javascript:void(0);" page_title="予定詳細" page_form="dialog" page_url="/calendars/{{$charge_student->current_calendar()->details()->id}}" role="button" class="btn btn-secondary btn-sm btn-flat">
                          {{$charge_student->current_calendar()->details()->status_name}}
                        </a>
                      @endif
                    @endif
                  </div>
                </div>

                <div calss="row text-sm">
                  @if($user->role!=="student")
                    @if($charge_student->student->is_hachiojisakura()==true && $charge_student->student->status != 'trial')
                      {{-- ①入会後から、予定管理のメニューを表示 --}}
                      <div class="col-6 float-left mt-1">
                        <a href="/students/{{$charge_student->student->id}}/schedule?list=month" role="button" class="btn btn-primary btn-sm btn-flat btn-block">
                          <i class="fa fa-calendar-check mr-1"></i>{{__('labels.month_schedule_list')}}
                        </a>
                      </div>
                      <div class="col-6 float-left mt-1">
                        <a href="/students/{{$charge_student->student->id}}/schedule?list=confirm" role="button" class="btn btn-warning btn-sm btn-flat btn-block">
                          <i class="fa fa-hourglass mr-1"></i>{{__('labels.adjust_schedule_list')}}
                        </a>
                      </div>
                      <div class="col-6 float-left mt-1">
                        <a href="/students/{{$charge_student->student->id}}/schedule?list=rest_contact" role="button" class="btn btn-danger btn-sm btn-flat btn-block">
                          <i class="fa fa-calendar-times mr-1"></i>{{__('labels.rest_contact')}}
                        </a>
                      </div>
                      <div class="col-6 float-left mt-1">
                        <a href="/students/{{$charge_student->student->id}}/schedule?list=exchange" role="button" class="btn btn-success btn-sm btn-flat btn-block">
                          <i class="fa fa-exchange-alt mr-1"></i>{{__('labels.exchange_schedule_list')}}
                        </a>
                      </div>
                    @endif

                    @if($charge_student->student->is_hachiojisakura())
                      @if($charge_student->student->status == 'trial')
                      {{-- ②体験申し込み後から、入会までは体験申し込み状況を表示する --}}
                      <div class="col-12 float-left mt-1">
                        @if(count($charge_student->student->trials)>0 && $charge_student->student->trials[0]!=null)
                        <a href="javascript:void(0);"
                        page_title="体験申込状況" page_form="dialog"
                        page_url="/trials/{{$charge_student->student->trials[0]->id}}/dialog?student_parent_id={{$item->id}}"
                        role="button" class="btn btn-primary btn-sm btn-flat btn-block">
                          <i class="fa fa-check-circle mr-1"></i>
                          体験申込状況
                        </a>
                        @endif
                      </div>
                      @else
                      {{-- ③入会後 --}}
                      <div class="col-6 float-left mt-1">
                        <a href="javascript:void(0);" page_form="dialog" page_url="/students/{{$charge_student->id}}/edit" page_title="{{__('labels.students')}}{{__('labels.setting')}}" role="button" class="btn btn-default btn-sm btn-flat btn-block">
                          <i class="fa fa-user-edit mr-1"></i>{{__('labels.students')}}{{__('labels.setting')}}
                        </a>
                      </div>
                      <div class="col-6 float-left mt-1">
                        <a href="javascript:void(0);" page_form="dialog" page_url="/students/{{$charge_student->id}}/create_login_info" page_title="{{__('labels.login_setting')}}" role="button" class="btn  btn-sm btn-flat btn-block
                        @if($charge_student->student->status == 'unsubscribe')
                         btn-secondary
                         disabled
                        ">
                          <i class="fa fa-ban mr-1"></i>{{__('labels.login_setting')}}
                        @else
                        btn-info
                        ">
                          <i class="fa fa-key mr-1"></i>{{__('labels.login_setting')}}
                        @endif
                        </a>
                      </div>
                      @if($charge_student->student->enable_agreements_by_type('normal')->count() > 0)
                      <div class="col-12 float-left mt-1">
                        <a title="契約情報" href="javascript:void(0);" page_title="ご契約内容" page_form="dialog" page_url="/students/{{$charge_student->id}}/agreement" role="button" class="btn btn-default btn-sm btn-flat btn-block">
                          <i class="fa fa-address-card mr-1"></i>ご契約内容
                        </a>
                      </div>
                      @endif
                      {{--
                      <div class="col-6 float-left mt-1">
                        <a title="{{__('labels.late_arrival_description')}}" href="javascript:void(0);" page_title="{{__('labels.late_arrival')}}" page_form="dialog" page_url="/students/{{$charge_student->id}}/late_arrival" role="button" class="btn btn-danger btn-sm btn-flat btn-block">
                          <i class="fa fa-exclamation-triangle mr-1"></i>{{__('labels.late_arrival')}}
                        </a>
                      </div>
                      --}}
                      @endif
                    @else
                    {{-- ④体験申し込みしていない＝申し込みリンク表示 --}}
                    <div class="col-12 float-left mt-1">
                      <a href="javascript:void(0);"
                      page_title="体験授業申し込み" page_form="dialog"
                      page_url="/parents/{{$item->id}}/trial_request?student_id={{$charge_student->id}}"
                      role="button" class="btn btn-primary btn-sm btn-flat btn-block">
                        <i class="fa fa-hand-point-right mr-1"></i>
                        体験授業申し込み
                      </a>
                    </div>
                    @endif
                  @endif
                </div>

              </li>
              @endforeach
            </ul>
            @else
            <div class="row p-2">
              <div class="col-12">
                <div class="callout callout-info mb-2">
                  <h5><i class="fa fa-exclamation-triangle mr-1"></i>まだ生徒が登録されていません</h5>
                  <p>学習管理する生徒の情報を登録してください</p>
                </div>
              </div>
            </div>
            @endif
            <div class="row p-2">
              <div class="col-12 text-right">
                <a class="btn btn-info" href="javascript:void(0);" page_form="dialog" page_url="/students/create?student_parent_id={{$item->id}}" page_title="生徒追加">
                  <i class="fa fa-plus"></i>
                  <span class="btn-label">生徒登録</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- /.card -->
    </div>
    <div class="col-12">
    @component('parents.forms.agreement', ['item' => $item, 'domain' => $domain])
      @slot('messages')
      <div class="col-12 p-2">
        <a href="/{{$domain}}/{{$item->id}}/messages" class="btn btn-flat btn-sm btn-block btn-primary">
          <i class="fa fa-envelope mr-1"></i>受信メッセージ一覧
        </a>
      </div>
      @endslot
    @endcomponent
    {{--
    @foreach($item->relations as $relation)
    @component('students.forms.agreement', ['item' => $relation->student, 'domain' => $domain]) @endcomponent
    @endforeach
    --}}
    </div>
  </div>
</section>
{{--まだ対応しない
<section class="content-header">
	<div class="row">
		<div class="col-12 col-md-6">
			@yield('milestones')
		</div>
		<div class="col-12 col-md-6">
			@yield('events')
		</div>
	</div>
</section>

<section class="content">
	@yield('tasks')
</section>
--}}
@endsection
