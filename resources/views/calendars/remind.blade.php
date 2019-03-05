@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
  @if($item['status']==='rest')
  講師あてに休み連絡を再送します。
  @elseif($item['status']==='confirm')
  生徒あてに授業予定確認をを再送します。
  @elseif($item['status']==='fix')
  生徒、講師あてに授業予定を再送します。
  @endif
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}/remind">
    @csrf
    @method('PUT')
    @if(isset($student_id))
      <input type="hidden" value="{{$student_id}}" name="student_id" />
    @endif
  <div class="row">
    <div class="col-12 col-lg-6 col-md-6 mb-1">
        <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_action">
          <i class="fa fa-envelope mr-1"></i>
            再送する
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
