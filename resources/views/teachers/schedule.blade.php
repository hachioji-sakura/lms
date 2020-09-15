@section('title')
  {{$domain_name}} {{__('labels.schedule_list')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('schedule_list_pager')
{{$calendars->appends(Request::query())->links('components.paginate')}}
@endsection

@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            <i class="fa fa-calendar mr-1"></i>
            @if($list=="today")
              {{__('labels.today_schedule_list')}}
            @elseif($list=="month")
              {{__('labels.month_schedule_list')}}
            @elseif($list=="confirm")
              {{__('labels.adjust_schedule_list')}}
            @elseif($list=="confirm")
              {{__('labels.adjust_schedule_list')}}
            @elseif($list=="cancel")
              {{__('labels.rest_schedule_list')}}
            @elseif($list=="exchange")
              {{__('labels.exchange_schedule_list')}}
            @elseif($list=="history")
              {{__('labels.schedule_history')}}
            @else
              {{__('labels.schedule_list')}}
            @endif
          </h3>
          <div class="card-title text-sm">
            @yield('schedule_list_pager')
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if($list=="exchange")
            @component('teachers.forms.exchange_schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'teacher' => $item]) @endcomponent
          @else
            @component('teachers.forms.schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'teacher' => $item]) @endcomponent
          @endif
        </div>
        <div class="card-header">
          <div class="card-title text-sm">
            @yield('schedule_list_pager')
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  @component('calendars.filter', ['domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'item' => $item, 'filter'=>$filter, 'is_list' => true])
  @endcomponent
  @endslot
@endcomponent
@endsection
