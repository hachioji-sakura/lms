@section('title')
  {{$domain_name}}ダッシュボード
@endsection
@extends('dashboard.common')
@include($domain.'.menu')


@section('contents')
<section id="member" class="content-header">
  <div class="card">
    <div class="">
      @component('components.profile', ['item' => $item, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
          @slot('courtesy')
          @endslot
          @slot('alias')
          <h6 class="widget-user-desc">
            @foreach($item->user->tags as $tag)
              @if($tag->tag_key=="manager_no")
                <small class="badge badge-dark mt-1 mr-1">
                  No.{{$tag->name()}}
                </small>
              @endif
              @if($tag->tag_key=="manager_type" && $tag->tag_value!='disabled')
                <small class="badge badge-danger mt-1 mr-1">
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
        @if($user->role=='manager')
        <li class="nav-item mr-1">
          <a class="nav-link btn btn-sm btn-default {{$view == 'home' ? 'active' : ''}}" href="/{{$domain}}/{{$item->id}}">
            <small>
              <i class="fa fa-user-shield"></i>
              {{__('labels.menu')}}
            </small>
          </a>
        </li>
        @endif
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
      <div class="col-12 col-lg-4 col-md-6 mb-1">
        <a href="/asks?search_type[]=agreement_update&search_status[]=new">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fa fa-handshake"></i>
          </span>
          <div class="info-box-content text-dark">
            <b class="info-box-text text-lg">契約変更依頼</b>
            <span class="text-sm">未処理の契約変更依頼</span>
          </div>
        </div>
        </a>
      </div>
    </div>
	</div>
</section>

@endsection
