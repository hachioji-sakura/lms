@section('title')
  {{$domain_name}}詳細
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
	<div class="container-fluid">
		<div class="row">
      <div class="col-md-6">
        @component('trials.forms.trial_detail',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
        @component('trials.forms.trial_student',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
        @component('trials.forms.trial_week_time',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
      </div>
      <div class="col-md-6">
        @component('trials.forms.trial_calendar',['item'=>$item, 'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name]) @endcomponent
      </div>
		</div>
	</div>
</section>
@endsection
