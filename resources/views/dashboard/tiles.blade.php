@section('contents')
    <div class="card-header">
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      <div id="listTable" class="card-body card-list" alt="CardTable">
        @if(count($items) > 0)
        <ul class="mailbox-attachments clearfix row">
          @foreach($items as $item)
          <li class="col-lg-3 col-md-4 col-12 @if($item->user->status===9) bg-gray @endif" accesskey="" target="">
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
            </div>
            <div class="row my-2">
              @if($item->user->status===0 && !($domain=="managers" && $item->id===1))
              <div class="col-6 col-md-6">
                <a class="btn btn-success btn-sm w-100" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="編集">
                  <i class="fa fa-edit"></i>
                  <span class="d-lg-block">編集</span>
                </a>
              </div>
              @elseif($domain!="students" && $item->user->status===1)
              <div class="col-6 col-md-6">
                <a class="btn btn-primary btn-sm w-100" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/remind" page_title="remind">
                  <i class="fa fa-envelope"></i>
                  <span class="d-lg-block">Remind</span>
                </a>
              </div>
              @endif
              @if($user->role!=="parent" && !($domain=="managers" && $item->id===1) && $item->user->status!==9)
              <div class="col-6 col-md-6">
                <a class="btn btn-danger btn-sm w-100" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/delete" page_title="削除">
                  <i class="fa fa-trash-alt"></i>
                  <span class="d-lg-block">削除</span>
                </a>
              </div>
              @endif
            </div>
          </li>
          @endforeach
        </ul>
        @else
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
        </div>
        @endif
      </div>
    </div>
@endsection
