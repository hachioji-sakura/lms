@section('title')
  {{$domain_name}}{{__('labels.dashboard')}}
@endsection
@extends('dashboard.common')
@include($domain.'.menu')

@section('contents')
<section class="content-header">
  <div class="card">
    <div class="">
      @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
          @slot('courtesy')
          @endslot
          @slot('alias')
            <h6 class="widget-user-desc">
              @foreach($item->user->tags as $tag)
                @if($tag->tag_key=="teacher_no")
                  <small class="badge badge-dark mt-1 mr-1">
                    {{$tag->keyname()}}{{$tag->name()}}
                  </small>
                @endif
                @if($tag->tag_key=="lesson")
                  <small class="badge badge-primary mt-1 mr-1">
                    {{$tag->name()}}
                  </small>
                @endif
                @if($user->role==="manager" && $tag->tag_key=="teacher_character")
                  <small class="badge badge-info mt-1 mr-1">
                    {{$tag->name()}}
                  </small>
                @endif
              @endforeach
            </h6>
          @endslot
      @endcomponent
    </div>
    <div class="card-body">
      <ul class="nav nav-pills ml-auto float-left mb-2">
        <li class="nav-item mr-1">
          <a class="nav-link btn btn-sm btn-default {{$view == 'home' ? 'active' : ''}}" href="/{{$domain}}/{{$item->id}}">
            <small>
              <i class="fa fa-home"></i>
              {{__('labels.menu')}}
            </small>
          </a>
        </li>
        <li class="nav-item mr-1">
          <a class="nav-link btn btn-sm btn-default {{$view == 'setting_menu'  ? 'active ': ''}}" href="/{{$domain}}/{{$item->id}}?view=setting_menu">
            <small>
              <i class="fa fa-cog"></i>
              {{__('labels.setting')}}
            </small>
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" >
            @yield('sub_contents')
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
