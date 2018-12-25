<div id="{{$domain}}_delete">
  @if(isset($page_message))
  <h6>{{$page_message}}</h6>
  @elseif($_del)
  <h6>以下の項目を削除してもよろしいですか？</h6>
  @endif
  @foreach($fields as $key=>$field)
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          {{$item[$key]}}
        </div>
      </div>
    </div>
  @endforeach
  @if(isset($forms))
    {{$forms}}
  @else
  <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @csrf
  @if(isset($_page_origin))
    <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
  @endif
  <div class="row">
    @if($_del)
      @method('DELETE')
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_delete">
              削除する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    @else
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              戻る
          </button>
      </div>
    @endif
  </div>
  </form>
  @endif
</div>
