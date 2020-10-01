@section('title')
{{__('labels.ask_list')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section class="content">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title" id="charge_students">
            @if($list=="lecture_cancel")
              <i class="fa fa-envelope-square mr-1"></i>
              {{__('labels.ask_lecture_cancel')}}
            @elseif($list=="teacher_change")
              <i class="fa fa-exchange-alt mr-1"></i>
              {{__('labels.ask_teacher_change')}}
            @elseif($list=="unsubscribe")
              <i class="fa fa-user-slash mr-1"></i>
              {{__('labels.ask_unsubscribe')}}
            @elseif($list=="recess")
              <i class="fa fa-pause-circle mr-1"></i>
              {{__('labels.ask_recess')}}
            @else
              <i class="fa fa-phone mr-1"></i>
              {{__('labels.ask_list')}}
            @endif
          </h3>
          <div class="card-title text-sm">
            @component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
              @slot("addon_button")
              @if( ($user->role=='parent' || $user->role=="manager") && $list != 'teacher_change' )
              <ul class="pagination pagination-sm m-0 float-left text-sm">
                <li class="page-item">
                  <a class="btn btn-info btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/ask/create" page_title="お問い合わせ登録">
                    <i class="fa fa-plus"></i>
                    <span class="btn-label">
                      {{__('labels.add')}}
                    </span>
                  </a>
                </li>
              </ul>
              @endif
              @endslot
            @endcomponent
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          @if(count($asks) > 0)
          <ul class="mailbox-attachments clearfix row">
            @foreach($asks as $ask)
            <?php
            $target_model_data = $ask->get_target_model_data();
            ?>
            <li class="col-12" accesskey="" target="">
              <div class="row">
                <div class="col-7 mt-1 text-lg">
                  <a href="/{{$domain}}/{{$item->id}}/ask/{{$ask->id}}" >
                    {{$ask["type_name"]}}
                  </a>
                </div>
                <div class="col-5 mt-1 text-right">
                  <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$ask->status]}} mr-1">
                    {{$ask->status_name()}}
                  </small>
                  <small title="{{$item["id"]}}" class="badge badge-info mr-1">
                    <i class="fa fa-comment-dots"></i> {{count($ask->comments)}}
                  </small>
                </div>
                <div class="col-12 col-md-5 text-muted">
                  @if($ask->target_model=='students')
                    <a href="/students/{{$target_model_data->id}}" target="_blank">
                    生徒氏名:{{$target_model_data->name()}} 様
                    </a>
                    <br>
                  @endif
                  @if($ask->type=='recess')
                    {{__('labels.duration')}}:{{$ask["duration"]}}
                  @elseif($ask->type=='unsubscribe')
                    {{__('labels.unsubscribe')}}{{__('labels.day')}}:{{$ask["start_date"]}}
                  @else
                  @endif
                </div>
                <div class="col-12 col-md-7 text-muted text-right " style="font-size:0.7rem;">
                  @if(($domain=="teachers" || $domain=="managers") && ($user->role=="teacher" || $user->role=="manager"))
                  <i class="ml-2 fa fa-user-edit"></i> {{__('labels.charge_user')}}：{{$ask["charge_user_name"]}}
                  <i class="ml-2 fa fa-user-check"></i> {{__('labels.target_user')}}：{{$ask["target_user_name"]}}
                  <i class="ml-2 fa fa-pen"></i> {{__('labels.create_user')}}：{{$ask["create_user_name"]}}
                  @endif
                  <i class="ml-2 fa fa-clock"></i>起票日:{{$ask->created_at_label('Y年m月d日')}}
                </div>
                <div class="col-12 text-xs mt-1" title="{{$ask->body}}">
                  {!!nl2br(str_limit($ask->body, 42,'...'))!!}
                </div>
                <div class="col-12 text-sm mt-1 text-right">
                  @component('teachers.forms.ask_button', ['item'=>$item, 'ask' => $ask, 'user'=>$user, 'domain'=>$domain, 'domain_name'=>$domain_name])
                  @endcomponent
                </div>
            </li>
            @endforeach
          </ul>
          @else
          <div class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
  @slot("search_form")
  <div class="col-12 col-md-4 mb-2">
    <label for="search_type" class="w-100">
      {{__('labels.ask_type')}}
    </label>
    <div class="w-100">
      <select name="search_type[]" class="form-control select2" width=100% multiple="multiple" >
        @foreach(config('attribute.ask_type') as $index => $name)
          <option value="{{$index}}"
          @if(isset($filter['search_type']) && in_array($index, $filter['search_type'])==true)
          selected
          @endif
          >{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="col-12 col-md-4 mb-2">
    <label for="search_status" class="w-100">
      {{__('labels.status')}}
    </label>
    <div class="w-100">
      <select name="search_status[]" class="form-control select2" width=100% multiple="multiple" >
        @foreach(config('attribute.ask_status') as $index => $name)
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
