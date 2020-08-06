@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-lg-4">
        @component('trials.forms.trial_student',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
        @component('trials.forms.trial_detail',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
        @component('trials.forms.trial_week_time',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
      </div>
      <div class="col-lg-8">
        <div class="card card-widget mb-2">
          <div class="card-header">
            <i class="fa fa-calendar mr-1"></i>体験授業予定
            <a role="button" class="btn btn-sm btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar">
              <i class="fa fa-plus mr-1"></i>
              体験授業登録
            </a>
          </div>
          <div class="card-footer">
            @component('trials.forms.trial_calendar',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
          <div class="card-footer text-right">
            {{-- TODO : 実用化されるまでコメントアウト
            <a class="btn btn-sm btn-flat btn-danger ml-2" role="button"  href="javascript:void(0);" page_title="希望日時変更をお願いする" page_form="dialog" page_url="/trials/{{$item["id"]}}/ask_candidate">
              <i class="fa fa-envelope mr-1"></i>
              体験希望日時変更の依頼
            </a>
            --}}
            <a class="btn btn-sm btn-flat btn-success ml-2" role="button"  href="javascript:void(0);" page_title="入会希望を受け取る連絡を出す" page_form="dialog" page_url="/trials/{{$item["id"]}}/ask_hope_to_join">
              <i class="fa fa-envelope mr-1"></i>
              入会希望に関するご連絡
            </a>
          </div>
        </div>

        @if($item->is_trial_lesson_complete()==true)
        <div class="card card-widget mb-2">
          <div class="card-header">
            <i class="fa fa-clock mr-1"></i>通常授業設定
            <a role="button" class="btn btn-sm btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar_setting">
              <i class="fa fa-plus mr-1"></i>
              通常授業登録
            </a>
          </div>
          <div class="card-footer">
            @component('trials.forms.user_calendar_setting',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
            @if($item->is_regular_schedule_fix()==true)
            <div class="row mt-2">
              <div class="col-12">
                <a href="javascript:void(0);" page_title="入会案内を連絡メール" page_form="dialog" page_url="/trials/{{$item->id}}/admission" role="button" class="btn btn-sm btn-success float-right mx-1 text-center">
                  <i class="fa fa-envelope"></i>
                  入塾案内連絡
                </a>
              </div>
            </div>
            @endif
          </div>

        </div>
        @endif

      </div>
		</div>
	</div>
</section>
@endsection
