@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
      このコメントを公開しますか？
  @endslot
  @slot('forms')
  <form method="POST" action="/comments/{{$item['id']}}/publiced">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div class="col-12 col-md-6 mb-1">
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="{{$domain}}_action">
          <i class="fa fa-lock-open mr-1"></i>
            公開する
        </button>
    </div>
    <div class="col-12 col-md-6 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.close_button')}}

        </button>
    </div>
  </div>
  </form>
  @endslot
@endcomponent
