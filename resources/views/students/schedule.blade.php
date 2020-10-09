@section('title')
  {{$domain_name}}授業スケジュール
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('schedule_list_pager')
{{$calendars->appends(Request::query())->links('components.paginate')}}
  @if($list=="month")
  <ul class="pagination pagination-sm m-0 float-left text-sm">
    <li class="page-item float-left">
      <a class="btn btn-default btn-sm" href="/{{$domain}}/{{$item->id}}/schedule?list=month&list_date={{date('Y-m-1', strtotime('-1 month '.$list_date))}}">
        <i class="fa fa-chevron-left mr-1"></i>
        <span class="btn-label">前月</span>
      </a>
    </li>
    <li class="page-item ml-1">
      <a class="btn btn-default btn-sm" href="/{{$domain}}/{{$item->id}}/schedule?list=month&list_date={{date('Y-m-1', strtotime('+1 month '.$list_date))}}">
        <span class="btn-label">次月</span>
        <i class="fa fa-chevron-right ml-1"></i>
      </a>
    </li>
  </ul>
  @endif
@endsection

@section('contents')
<section class="content mb-2">
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
          </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if($list=="exchange")
            @component('students.forms.exchange_schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'item' => $item]) @endcomponent
          @else
            @component('students.forms.schedule_list', ['calendars' => $calendars, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes, 'user'=>$user, 'item' => $item]) @endcomponent
          @endif
        </div>
      </div>
      <!-- /.card -->
      <div class="card-header">
        <div class="card-title text-sm">
          @yield('schedule_list_pager')
        </div>
      </div>
    </div>
  </div>
</section>

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_from_date" class="w-100">
        {{__('labels.date')}}(FROM)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_from_date" name="search_from_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_from_date']))
          value="{{$filter['search_from_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="form-group">
      <label for="search_to_date" class="w-100">
        {{__('labels.date')}}(TO)
      </label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
        <input type="text" id="search_to_date" name="search_to_date" class="form-control float-left" uitype="datepicker" placeholder="2000/01/01"
        @if(isset($filter['search_to_date']))
          value="{{$filter['search_to_date']}}"
        @endif
        >
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <div class="form-group">
      <label for="is_exchange" class="w-100">
        {{__('labels.sort_no')}}
      </label>
      <label class="mx-2">
      <input type="checkbox" value="1" name="is_desc" class="icheck flat-green"
      @if(isset($filter['sort']['is_desc']) && $filter['sort']['is_desc']==true)
        checked
      @endif
      >{{__('labels.date')}} {{__('labels.desc')}}
      </label>
    </div>
  </div>
  <div class="col-12 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
    <div class="w-100">
      <select name="search_status[]" class="form-control select2" width=100% placeholder="検索ステータス" multiple="multiple" >
        @foreach($attributes['calendar_status'] as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_status']) && in_array($index, $filter['search_status'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @endslot
@endcomponent
@endsection
