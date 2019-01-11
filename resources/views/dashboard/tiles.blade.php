@section('contents')
    <div class="card-header">
      <h3 class="card-title">@yield('title')</h3>
    </div>
    <div class="card-body">
      <div id="listTable" class="card-body card-list" alt="CardTable">
        @if(count($items) > 0)
        <ul class="mailbox-attachments clearfix row">
          @foreach($items as $item)
          <li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
            <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
            <input type="hidden" value="{{$item->id}}" name="id">
            <div class="row">
              <div class="col-12 text-center">
                <a href="./{{$domain}}/{{$item->id}}">
                  <img src="{{$item->icon}}" class="mw-192px w-50">
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-12 text-lg">
                <a href="./{{$domain}}/{{$item->id}}">
                  <ruby style="ruby-overhang: none">
                    <rb>{{$item->name}}</rb>
                    <rt>{{$item->kana}}</rt>
                  </ruby>
                </a>
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
