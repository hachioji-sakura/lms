@section('contents')
    <div class="card-header">
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      <div id="listTable" class="card-body card-list" alt="CardTable">
        @if(count($items) > 0)
        <ul class="mailbox-attachments clearfix row">
          @foreach($items as $item)
          <li class="col-lg-3 col-md-4 col-12 " accesskey="" target="">
            <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
            <input type="hidden" value="{{$item->id}}" name="id">
            <div class="row">
              <div class="col-12 text-center">
                <a href="./{{$domain}}/{{$item->id}}">
                  <img src="{{$item->icon}}" class="img-circle elevation-2 mw-192px w-50">
                </a>
              </div>
            </div>
            <div class="row my-2">
              <div class="col-12 text-lg text-center">
                <a href="./{{$domain}}/{{$item->id}}" role="button" class="btn btn-primary btn-block btn-lg float-left mr-1">
                    {{$item->name}}
                </a>
              </div>
            </div>
            {{--
            <div class="mailbox-attachment-info">
            	<span class="mailbox-attachment-size">
                <a class="btn btn-default btn-sm float-right" href="javascript:void(0);" page_form="dialog" page_url="/students/{{$item->id}}/edit" page_title="生徒情報編集">
                  <i class="fa fa-comment-dots"></i>プロフィール編集
                </a>
            	</span>
            </div>
            --}}
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
