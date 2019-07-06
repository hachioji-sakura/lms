@component('components.page', ['item' => $item, 'fields' => [], 'domain' => $domain])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
  下記の授業内容にて、ご契約者様へ入会案内のご連絡をしますか？
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
    @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $item]) @endcomponent
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
  <div class="row">
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_admission">
      <form method="POST" action="/trials/{{$item['id']}}/admission">
        @csrf
        <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_admission">
          <i class="fa fa-envelope mr-1"></i>
          送信する
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-6 col-md-6 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}

        </button>
    </div>
  </div>
  @endslot

@endcomponent
