@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
    @if($user->role==="teacher")
    以下の授業予定を確定しますか？
    @else
    以下の授業予定をご確認ください
    @endif
  @endslot
  @slot('forms')
  @method('PUT')
  <div class="row">
@if($user->role==="teacher")
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_confirm">
      <form method="POST" action="/calendars/{{$item['id']}}/confirm">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-success btn-block"  accesskey="{{$domain}}_confirm">
            <i class="fa fa-calendar-check mr-1"></i>
              予定確定
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_action">
      <form method="POST" action="/calendars/{{$item['id']}}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_action">
          <i class="fa fa-trash-alt mr-1"></i>
          予定削除
        </button>
      </form>
    </div>
@else
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_fix">
      <form method="POST" action="/calendars/{{$item['id']}}/fix">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-success btn-block"  accesskey="{{$domain}}_fix">
          <i class="fa fa-calendar-check mr-1"></i>
          この予定に出席します
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_cancel">
      <form method="POST" action="/calendars/{{$item['id']}}/cancel">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_cancel">
          <i class="fa fa-ban mr-1"></i>
          この予定に出席できません
        </button>
      </form>
    </div>
@endif
    <div class="col-12 col-lg-12 col-md-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            閉じる
        </button>
    </div>
  </div>
  @endslot
@endcomponent
