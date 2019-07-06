@section('contents')
    <div class="card-header">
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
      <div id="listTable" class="card-body card-list mt-1" alt="CardTable">
        @if(count($items) > 0)
        <ul class="mailbox-attachments clearfix row">
          @foreach($items as $item)
          <li class="col-lg-4 col-md-4 col-12 @if($item->user->status===9) bg-gray @endif" accesskey="" target="">
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
              </div>
              {{--
              <div class="col-12 text-sm">
                <i class="fa fa-envelope mr-1"></i>{{$item->user->email}}
              </div>
              --}}
            </div>
            <div class="row my-2">
              <div class="col-12">
              @if($domain!="students" && $item->user->status===1)
                <a class="btn btn-primary btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/remind" page_title="本登録連絡">
                  <i class="fa fa-envelope mr-1"></i>
                  {{__('labels.register_ask')}}
                </a>
              @endif
              @if($item->user->status===0 && !($domain=="managers" && $item->id===1))
              {{--
                <a class="btn btn-success btn-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="編集">
                  <i class="fa fa-edit"></i>
                  <span class="d-lg-block">
                    {{__('labels.edit')}}
                  </span>
                </a>
              --}}
              @endif
              @if($item->user->status===0 && $domain==="teachers" && $item->is_manager()===false)
                <a class="btn my-1 btn-info btn-sm text-sm" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/to_manager" page_title="事務兼務">
                  <i class="fa fa-users-cog mr-1"></i>
                  {{__('labels.additional_officer')}}
                </a>
              @endif
              @if($user->role!=="parent" && !($domain=="managers" && $item->id===1) && $item->user->status!==9)
                <a class="btn my-1 btn-danger btn-sm " href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/delete" page_title="削除">
                  <i class="fa fa-trash-alt mr-1"></i>
                  {{__('labels.delete')}}
                </a>
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
@endsection
