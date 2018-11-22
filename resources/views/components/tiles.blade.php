@foreach($items as $item)
<li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
  <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
  <input type="hidden" value="{{$item->id}}" name="id">
  <div class="row">
    <div class="col-12 text-center">
      <a href="./@yield('domain')/{{$item->id}}">
        <img src="{{$item->icon}}" class="mw-192px w-50">
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-lg">
      <a href="./@yield('domain')/{{$item->id}}">
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name}}</rb>
          <rt>{{$item->kana}}</rt>
        </ruby>
      </a>
    </div>
</li>
@endforeach
