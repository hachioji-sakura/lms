@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
  <div class="col-12 col-lg-12 col-md-12 mb-1">
    <h4 class="text-danger">体験授業完了済みの生徒の保護者様宛に、入会案内のメールを送信します。</h4>
  </div>
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
  <div id="{{$domain}}_action">
    <form method="POST" action="/trials/{{$item['id']}}/register">
    @csrf
    <div class="row">
      <div class="col-12 mb-1">
        <div class="form-group">
          <label for="status">
            別途ご連絡する内容(入会案内メールに、以下の内容が追記されます）
            <span class="right badge badge-secondary ml-1">任意</span>
            <textarea type="text" id="body" name="comment" class="form-control" placeholder="例：入会後も別のレッスンの体験授業を実施することは可能です。" ></textarea>
          </label>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action">
            <i class="fa fa-envelope mr-1"></i>
              入会案内メールの送信
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              閉じる
          </button>
      </div>
    </div>
    </form>
  </div>
  @endslot

@endcomponent
