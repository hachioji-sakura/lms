@isset($action)
<div id="{{$domain}}_{{$action}}">
@else
<div id="{{$domain}}_">
@endisset

@if(isset($page_message))
  <h6>{{$page_message}}</h6>
@elseif(isset($action) && $action=='delete')
<div class="col-12 col-lg-12 col-md-12 mb-1">
  <h4 class="text-danger">削除してもよろしいですか？</h4>
</div>
@elseif(isset($action) && $action=='remind')
<div class="col-12 col-lg-12 col-md-12 mb-1">
  <h4 class="text-success">本登録依頼メールを送信しますがよろしいですか？</h4>
</div>
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
  @if(isset($forms) && !empty(trim($forms)))
    {{-- 独自formを利用する場合 --}}
    {{$forms}}
  @else
    {{-- 共通formを利用する場合 --}}
    {{-- action=deleteのみ、mothod=DELETE --}}
    @if(isset($action) && $action!='delete')
      <form method="POST" action="/{{$domain}}/{{$item['id']}}/{{$action}}">
    @else
      <form method="POST" action="/{{$domain}}/{{$item['id']}}">
    @endif
    @csrf
  <div class="row">
    {{-- 共通form用のボタン --}}
    @if(isset($action) && $action=='delete')
      @method('DELETE')
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}">
            <i class="fa fa-trash mr-1"></i>
              削除する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    @elseif(isset($action) && $action=='remind')
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-success btn-block"  accesskey="{{$domain}}_{{$action}}">
            <i class="fa fa-envelope mr-1"></i>
              送信する
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    @else
      <div class="col-12 mb-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          閉じる
        </a>
      </div>
    @endif
  </div>
  </form>
  @endif
</div>
