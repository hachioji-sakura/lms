@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
  この授業予定をキャンセルしますが、よろしいですか？
  @endslot
  @slot('forms')
  @method('PUT')
  <form method="POST" action="/calendars/{{$item['id']}}/cancel">
    @csrf
  @if(isset($_page_origin))
    <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
  @endif
  <div class="row">
      @method('PUT')
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_delete">
              キャンセル連絡
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              閉じる
          </button>
      </div>
  </div>
  </form>
  @endslot
@endcomponent
