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
        <input type="hidden" name="mail_template" value="entry" />

{{-- TODO メールテンプレート選択は不要
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
--}}
{{-- TODO 送信予定の選択は不要
      <div class="col-12 mt-1">
        <label for="mail_template">
          送信予定日時
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <div class="input-group">
          <input type="text" name="send_day" class="form-control float-left w-40" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
           value="{{date('Y/m/d')}}"
           minvalue="{{date('Y/m/d')}}"
          >
          <div class="text-center float-left mr-2"> </div>
          <select name="send_hour" class="form-control float-left mr-1 w-20">
            @for ($h = 8; $h < 23; $h++)
              <option value="{{$h}}"
              @if(($h==date('H') && date('i') < 55) || ($h-1==date('H') && date('i') >= 55))
              selected
              @endif
              >{{str_pad($h, 2, 0, STR_PAD_LEFT)}}時</option>
            @endfor
          </select>
          <select name="send_minutes" class="form-control float-left mr-1 w-20">
            @for ($m = 0; $m < 12; $m++)
              <option value="{{$m*5}}"
              @if(($m-1)*5 <= date('i') && ($m)*5 > date('i'))
              selected
              @endif
              >{{str_pad($m*5, 2, 0, STR_PAD_LEFT)}}分</option>
              @endfor
          </select>
        </div>
      </div>
--}}
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
