<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body">
  @component('components.search_word', ['search_word' => $search_word, 'is_filter_button' => true])
  @endcomponent
  <div id="listTable" class="card-body card-list mt-1 " alt="CardTable">
    @if(count($items) > 0)
    <ul class="mailbox-attachments clearfix row">
      @foreach($items as $item)
      <li class="col-lg-4 col-md-4 col-12 @if($item->status==='unsubscribe') bg-gray @else bg-white @endif" accesskey="" target="">
        <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
        <input type="hidden" value="{{$item->id}}" name="id">
        <div class="row">
          <div class="col-12 text-center">
            <a href="./{{$domain}}/{{$item->id}}">
              <img src="{{$item->user->image->s3_url}}" class="img-circle elevation-2 mw-128px w-50">
            </a>
          </div>
        </div>
        <div class="row my-2">
          <div class="col-12 text-lg text-center">
            <a href="./{{$domain}}/{{$item->id}}" class="">
                {{$item->name()}}
            </a>
            <span class="text-xs ml-1">
              <small class="badge badge-{{config('status_style')[$item->status]}} mt-1 mr-1">
                {{$item->status_name()}}
              </small>
            </span>
            @if ($domain == "parents" && $item->enable_agreements_by_type('normal')->count() == 0)
            <span class="text-xs ml-1">
              <span class="badge badge-secondary mt-1 mr-1">
                {{__('labels.no_agreements')}}
              </span>
            </span>
            @endif

            <br>
            @foreach($fields as $field=>$style)
            <span class="text-xs ml-1">
              @if($field=='is_admin')
                @if($item->is_admin()==true)
                  <small class="badge badge-{{$style}} mt-1 mr-1">
                    管理者権限
                  </small>
                @endif
                @continue
              @endif
              <small class="badge badge-{{$style}} mt-1 mr-1">
                @if($field=='grade')
                  {{$item->grade()}}
                @elseif($field=='lesson')
                  {{$item->lesson()}}
                @endif
              </small>
            </span>
            @endforeach
          </div>
        </div>
        <div class="row my-2">
          <div class="col-12">
          @if ($domain == "students" && $item->enable_agreements_by_type('normal')->count() > 0)
            <a title="契約情報" href="javascript:void(0);" page_title="ご契約内容" page_form="dialog" page_url="/students/{{$item->id}}/agreement" role="button" class="btn btn-primary btn-sm my-1 text-sm">
              <i class="fa fa-address-card mr-1"></i>ご契約内容
            </a>
          @endif
            {{-- TODO user->status==1にて判断する必要がある。移行データが、regularかつ初回の登録依頼を出すために、user->status=1としているため --}}
            {{-- TODO status=regular かつ、user->status=1として、移行データのときだけ表示するほうが安全 --}}
          @if($domain!="students" && ($item->status==='trial' || $item->user->status==1))
            <a class="btn btn-primary btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/remind" page_title="本登録連絡">
              <i class="fa fa-envelope mr-1"></i>
              {{__('labels.register_ask')}}
            </a>
          @endif
          @if($item->status==='regular' && !($domain=="managers" && $item->id===1))
          {{--
            <a class="btn btn-success btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="編集">
              <i class="fa fa-edit"></i>
              <span class="d-lg-block">
                {{__('labels.edit')}}
              </span>
            </a>
          --}}
          @endif
          @if($item->status==='regular' && $domain==="teachers" && $item->is_manager()===false)
            <a class="btn my-1 btn-info btn-sm text-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/to_manager" page_title="事務兼務">
              <i class="fa fa-users-cog mr-1"></i>
              {{__('labels.additional_officer')}}
            </a>
          @endif
          @if(!($domain=="managers" && $item->id===1))
            @if($item->status!='regular' && $domain!="parents")
            <a class="btn my-1 btn-success btn-sm mr-1" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/regular" page_title="アカウントステータス更新">
              <i class="fa fa-user-check mr-1"></i>
              @if($domain=='teachers' || $domain=='managers')
              入社に戻す
              @else
              入会済みに戻す
              @endif
            </a>
            @endif
            @if($item->status!='unsubscribe')
            <a class="btn my-1 btn-danger btn-sm float-right mr-1" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/retirement" page_title="アカウントステータス更新">
              <i class="fa fa-user-slash mr-1"></i>
              @if($domain=='teachers' || $domain=='managers')
              {{__('labels.retirement')}}
              @else
              {{__('labels.unsubscribe')}}
              @endif
            </a>
            @endif
          @endif
        </div>
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
@yield('list_filter')
