<div id="{{$domain}}_delete">
  @if(isset($page_message))
  <h6>{{$page_message}}</h6>
  @elseif($_del)
  <h6>以下の項目を削除してもよろしいですか？</h6>
  @endif
  <div class="row">
  @if(isset($field_logic))
    {{$field_logic}}
  @else
    @foreach($fields as $key=>$field)
        @if(isset($field['size']))
        <div class="col-{{$field['size']}}">
        @else
        <div class="col-12">
        @endif
          <div class="form-group">
            @isset($field['format'])
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            {!! sprintf($field['format'], $item[$key]) !!}
            @else
              <label for="{{$key}}" class="w-100">
                {{$field['label']}}
              </label>
              {{$item[$key]}}
            @endisset
          </div>
        </div>
    @endforeach
  @endif
  </div>
  @if(!empty(trim($forms)))
    {{$forms}}
  @else
  <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @csrf
    @if(isset($_page_origin))
      <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
    @endif
  <div class="row">
    @if(isset($_del) && $_del==true)
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
              閉じる
          </button>
      </div>
    @endif
  </div>
  </form>
  @endif
</div>
