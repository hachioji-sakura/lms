<div id="{{$domain}}_{{$action}}">
  <div class="col-12 my-1">
    <h5 class="text-danger">
      {{$title}}ステータスへ更新しますか？
    </h5>
    <div class="alert alert-warning text-sm pr-2">
      <h5><i class="icon fa fa-exclamation-triangle"></i> {{__('labels.important')}}</h5>
      メール等で、本人宛に通知はしません。<br>
      この機能は、ステータス更新のみ行います。
    </div>
  </div>

  {{-- 詳細表示項目を羅列する --}}
  @if(isset($field_logic))
    {{$field_logic}}
  @else
    @component('components.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
    @endcomponent
  @endif
  <form method="POST" action="/{{$domain}}/{{$item['id']}}/{{$action}}">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      {{-- 共通form用のボタン --}}
      <div class="col-12 col-md-6 my-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_{{$action}}" confirm="退職ステータスに更新しますか？">
            <i class="fa fa-sign-out-alt mr-1"></i>
              {{$title}}ステータスに更新
          </button>
      </div>
      <div class="col-12 col-md-6 my-1">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.close_button')}}
        </a>
      </div>
    </div>
  </form>
</div>
