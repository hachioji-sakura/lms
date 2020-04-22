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
      @foreach(config('attribute.message_status') as $index => $name)
      <li class="nav-item">
         <a href="/{{$user->domain}}/{{$user->id}}/messages?search_status={{$index}}" class="nav-link @if(isset($filter['search_status']) && $index===$filter['search_status']) active @endif">
           <i class="fa fa-envelope nav-icon"></i>{{$name}}
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection

@section('contents')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
       <div class="user-block">
         <h3>
           {{$root_message->title}}
         </h3>
         <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-12">
            <h3 class="card-title">From</h3>
            <img class="direct-chat-img" src="{{$items->first()->create_user->icon()}}" alt=""></img>
            <span class="user-name ml-3">
              {{$root_message->create_user->details()->name()}}
            </span>
          </div>
          <div class="col-2 d-md-block d-none">
            <i class="fas fa-long-arrow-alt-right fa-6x"></i>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-2">
            <h3 class="card-title">To</h3>
            <img class="direct-chat-img" src="{{$items->first()->target_user->icon()}}" alt=""></img>
            <span class="user-name ml-3">
              {{$root_message->target_user->details()->name()}}
            </span>
          </div>
        </div>
        <a href="javascript:void(0);" page_form="dialog" page_url="/messages/{{$items->first()->id}}/reply" page_title="{{__('labels.reply')}}" role="button" class="btn btn-primary btn-sm btn-block col-lg-1 col-sm-12">
          <i class="fa fa-envelope mr-1"></i>{{__('labels.reply')}}
        </a>
      </div>
      </h3>

      <div class="card-tools">

      </div>
    </div>
    <div class="card-body">
      {!!nl2br($root_message->body)!!}
    </div>
    <div class="card-footer">
      @if(count($items) > 1)
        <div class="direct-chat-messages">
        @foreach($items as $item)
          @if($loop->first)
          @else
            <div class="direct-chat-msg  {{$item->create_user_id == $root_message->create_user_id ? '' : 'right'}} {{$item->id == $id ? 'bg-primary' : ''}}">
              <div class="direct-chat-info clearfix">
                <span class="direct-chat-name {{$item->create_user_id == $root_message->create_user_id ? 'float-left' : 'float-right'}}">{{$item->create_user->details()->name()}}</span>
                <span class=" {{$item->create_user_id == $root_message->create_user_id ? 'float-right' : 'float-left'}}">{{$item->created_at}}</span>
              </div>
              <img class="direct-chat-img mr-2" src="{{$item->create_user->icon()}}" alt=""></img>
              <div class="direct-chat-text">
                {!!nl2br($item->body)!!}
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
