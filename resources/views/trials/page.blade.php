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
            <a role="button" class="btn btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar">
              <i class="fa fa-plus mr-1"></i>
              体験授業予定を設定する
            </a>
          </div>
          <div class="card-footer">
            @component('trials.forms.trial_calendar',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
        </div>

        <div class="card card-widget mb-2">
          <div class="card-header">
            <i class="fa fa-clock mr-1"></i>通常授業設定
            {{--
            <a class="btn btn-flat btn-danger float-right" role="button"  href="javascript:void(0);" page_title="{{$domain_name}}登録" page_form="dialog" page_url="/trials/{{$item["id"]}}/admission">
              <i class="fa fa-envelope mr-1"></i>入塾案内を出す
            </a>
            --}}
            <a role="button" class="btn btn-flat btn-info float-right" href="/trials/{{$item["id"]}}/to_calendar_setting">
              <i class="fa fa-plus mr-1"></i>
              通常授業予定を設定する
            </a>
          </div>
          <div class="card-footer">
            @component('trials.forms.user_calendar_setting',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
          </div>
        </div>

      </div>
		</div>
	</div>
</section>
@endsection
