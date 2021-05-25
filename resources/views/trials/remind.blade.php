@component('trials.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
  この予定の確認連絡をしますか？
  @endslot
  @slot('forms')
  @method('PUT')

  <div class="row">
    <div class="col-12 col-md-6 mb-1" id="{{$domain}}_fix">
      <form method="POST" action="/trials/{{$item['id']}}/remind">
        @csrf
        <input type="text" name="dummy" style="display:none;" / >
        @method('PUT')
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_fix">
          <i class="fa fa-envelope mr-1"></i>
          確認連絡を送信する
        </button>
      </form>
    </div>
    <div class="col-12 col-md-6 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}

        </button>
    </div>
  </div>
  @endslot
@endcomponent
