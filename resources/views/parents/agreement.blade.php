@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content mb-2">
  @component('parents.forms.agreement', ['item' => $item, 'domain' => $domain]) @endcomponent

  @foreach($item->relations as $relation)
  @component('students.forms.agreement', ['item' => $relation->student, 'domain' => $domain]) @endcomponent
  @endforeach
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
