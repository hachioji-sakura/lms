@extends('dashboard.common')

@section('title')
{{__('labels.agreement_statements')}}
@endsection

@section('title_header')
{{__('labels.agreement_statements')}}
@endsection

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
      <a class="nav-link" href="/agreements?status=commit">
        <i class="fa fa-arrow-left mr-1"></i>
        {{__('labels.agreements')}}{{__('labels.list')}}
      </a>
    </li>
    <li class="nav-item has-treeview menu-open mt-2">
      <a href="#" class="nav-link">
        <i class="nav-icon fa fa-shake_hands"></i>
        <p>
          {{__('labels.agreement_statements')}}
          <i class="right fa fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        @foreach($agreements as $agreement)
        <li class="nav-item">
          <a class="nav-link {{request()->has('agreement_id') && request()->get('agreement_id') == $agreement->id ? 'active': ''}}" href="/agreement_statements?agreement_id={{$agreement->id}}">
            {{$agreement->title}}{{$agreement->status_name}}
          </a>
        </li>
        @endforeach
      </ul>
    </li>
  </li>
</ul>
@endsection


@section('page_footer')
@endsection

@section('list_filter')
@endsection

@include('dashboard.lists')
