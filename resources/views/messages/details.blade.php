@extends('dashboard.common')

@section('title', $items->first()->title)

@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @foreach(config('attribute.message_box') as $index => $name)
      <li class="nav-item">
       <a href="/{{$user->domain}}/{{$user->id}}/messages?search_list={{$index}}" class="nav-link @if(isset($search_list) && $index===$search_list) active @endif">
         <i class="fa fa-{{$name['icon']}} nav-icon"></i>{{$name['name']}}
       </a>
     </li>
     @endforeach
    </ul>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-comment"></i>
      <p>
        {{__('labels.message')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="javascript:void(0);" page_form="dialog" page_url="/messages/{{$items->first()->id}}/reply" page_title="{{__('labels.reply')}}" class="nav-link">
          <i class="fa fa-reply mr-1 nav-icon"></i>{{__('labels.reply')}}
        </a>
      </li>
    </ul>
  </li>
</ul>
@endsection

@section('page_footer')
<dt>
  <a href="javascript:void(0);" page_form="dialog" page_url="/messages/{{$items->first()->id}}/reply" page_title="{{__('labels.reply')}}" class="btn btn-app">
    <i class="fa fa-reply mr-1"></i>{{__('labels.reply')}}
  </a>
</dt>
@endsection


@section('contents')
<div class="container-fluid">
  <div class="card card-primary card-outline">
    <div class="card-header p-0 pl-2">
      <div class="user-block">
        <div class="row text-sm text-muted">
          <div class="col-6">
            <i class="fa fa-paper-plane"></i>
            <span class="user-name ml-1">
            {{$item->create_user->details()->name()}} → {{$item->target_user->details()->name()}}
            </span>
          </div>
          <div class="col-6">
            <div class="card-tools text-sm text-muted float-right">
              <i class="fa fa-clock ml-1"></i>
              {{$item->dateweek_format($item->created_at,'Y/m/d H:m:s')}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-header">
      <div class="row">
        <div class="col-12">
          {{$item->title}}
        </div>
      </div>
    </div>
    <div class="card-body">
      {!!nl2br($root_message->body)!!}
      <div class="col-12">
        @if(!empty($root_message->s3_url))
          <br>
          <span class="mr-1">
            <a href="{{$root_message->s3_url}}" target="_blank">
              <i class="fa fa-link mr-1"></i>
              {{$root_message->s3_alias}}
            </a>
          </span>
        @endif
      </div>
    </div>
    <div class="card-footer">
      @if(count($items) > 1)
        <div class="direct-chat-messages">
        @foreach($items as $item)
          @if($loop->first)
          @else
            <div class="direct-chat-msg {{$item->create_user_id == $root_message->create_user_id ? '' : 'right'}}">
              <div class="direct-chat-info clearfix">
                <span class="direct-chat-name {{$item->create_user_id == $root_message->create_user_id ? 'float-left' : 'float-right'}}">{{$item->create_user->details()->name()}}</span>
                <span class=" {{$item->create_user_id == $root_message->create_user_id ? 'float-right' : 'float-left'}}">{{$item->created_at}}</span>
              </div>
              <img class="direct-chat-img mr-2" src="{{$item->create_user->icon()}}" alt=""></img>
              <div class="direct-chat-text  {{$item->id == $id ? 'bg-secondary' : ''}}">
                {!!nl2br($item->body)!!}
                @if(!empty($item->s3_url))
                  <br>
                  <span class="mr-1">
                    <a href="{{$item->s3_url}}" target="_blank">
                      <i class="fa fa-link mr-1"></i>
                      {{$item->s3_alias}}
                    </a>
                  </span>
                @endif
              </div>
            </div>
          @endif
        @endforeach
        </div>
      @else
      <div class="alert">
        <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
