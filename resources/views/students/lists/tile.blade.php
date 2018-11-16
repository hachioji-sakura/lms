<li class="col-lg-3 col-md-4 col-12" accesskey="" target="">
  <input type="hidden" value="{{$loop->index}}" name="__index__" id="__index__">
  <input type="hidden" value="{{$item->id}}" name="id">
  <div class="row">
    <div class="col-12 text-center">
      @if($item->gender===1)
        <img src="../img/school/man.png" style="max-width:60%;">
      @elseif($item->gender===2)
        <img src="../img/school/woman.png" style="max-width:60%;">
      @else
        <img src="../img/school/student.png" style="max-width:60%;">
      @endif
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-lg">
      <a href="./students/{{$item->id}}">
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name_last}} {{$item->name_first}}</rb>
          <rt>{{$item->kana_last}} {{$item->kana_first}}</rt>
        </ruby>
      </a>
    </div>
</li>
