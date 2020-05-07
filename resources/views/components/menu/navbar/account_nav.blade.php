@section('account_nav')
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <img src="{{$user->icon}}" class="img-size-32 mr-1 img-circle">
    <span class="d-none d-sm-inline-block">{{$user->name}}</span>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <a href="javascript:void(0);" class="dropdown-item">
      <div class="media">
        <img src="{{$user->icon}}" class="img-size-50 mr-3 img-circle">
        <div class="media-body">
          <h3 class="dropdown-item-title">
            {{$user->name}}
          </h3>
          <p class="text-sm">
            @if($user->role==="manager")
            <small class="badge badge-danger mt-1 mr-1">
            管理者権限
            </small>
            @else($user->role==="student")
            <small class="badge badge-info mt-1 mr-1">
            {{$user["role_name"]}}
            </small>
            @endif
          </p>
        </div>
      </div>
    </a>
    @if($user->role!=="student")
    <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.account_setting')}}" page_form="dialog" page_url="/{{$user["domain"]}}/{{$user->id}}/edit" >
      <i class="fa fa-user-edit mr-2"></i>{{__('labels.account_setting')}}
    </a>
    <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.change_email')}}" page_form="dialog" page_url="/{{$user["domain"]}}/{{$user->id}}/email_edit" >
      <i class="fa fa-envelope-square mr-2"></i>{{__('labels.change_email')}}
    </a>
    <a href="javascript:void(0);" class="dropdown-item"  page_title="{{__('labels.password_setting')}}" page_form="dialog" page_url="/password" >
      <i class="fa fa-lock mr-2"></i>{{__('labels.password_setting')}}
    </a>
    <a href="/faqs" class="dropdown-item" >
      <i class="fa fa-question-circle mr-2"></i>{{__('labels.faqs')}}
    </a>
    @endif
    @if($user->role!=="parent")
      @if(app()->getLocale()=='en')
      <a href="/home?locale=ja" class="dropdown-item" >
        <i class="fa fa-exchange-alt mr-2"></i>日本語
      </a>
      @else
      <a href="/home?locale=en" class="dropdown-item" >
        <i class="fa fa-exchange-alt mr-2"></i>English
      </a>
      @endif
    @endif
    <a class="dropdown-item" page_form="dialog" page_url="/icon/change?origin={{$domain}}&item_id={{$user->id}}&user_id={{$user->user_id}}" page_title="{{__('labels.icons')}}">
      <i class="fa fa-portrait mr-2"></i>{{__('labels.icons')}}
    </a>
    <div class="dropdown-divider"></div>
    <a href="/logout" class="dropdown-item">
      <i class="fa fa-sign-out-alt mr-2"></i>{{__('labels.logout')}}
    </a>
  </div>
</li>
@endsection
