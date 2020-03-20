@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action'=>'remind'])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
本登録依頼メールを送信しますがよろしいですか？
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')

  <form method="POST" action="/{{$domain}}/{{$item['id']}}/remind">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      @if(isset($item['email']))
        @component('students.forms.editable_email', ['item' => $item]) @endcomponent
      <div class="col-12 mt-1">
        <label for="mail_template">
          メールテンプレート
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <div class="form-group">
          <select name="mail_template">
            <option value="entry_202004">2020年4月リリース</option>
            <option value="entry">標準</option>
          </select>
        </div>
      </div>
        @if($domain=="teachers")
        <div class="col-12">
          <h6 class="text-sm p-1 pl-2 mt-1 bg-warning" >
            ※講師・事務を兼務する場合は、講師登録を先に行い<br>
            　講師一覧より、事務兼務にて設定します。
          </h6>
        </div>
        @endif
      @endif
      <div class="col-12 col-md-6 my-1">
          <button type="button" class="btn btn-submit btn-success btn-block" accesskey="{{$domain}}_remind" confirm="送信しますか？">
            <i class="fa fa-envelope mr-1"></i>
              送信する
          </button>
      </div>
      <div class="col-12 col-md-6 my-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          キャンセル
        </a>
      </div>
    </div>
</form>
@endslot
@endcomponent
