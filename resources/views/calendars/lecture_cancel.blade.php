@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    この授業の休講依頼を出しますか？
    <div class="col-12 col-lg-12 col-md-12 mb-1">
      <span class="text-danger">休講依頼承認後、休講の予定に更新されます。</span>
    </div>
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    <form method="POST" action="/calendars/{{$item['id']}}/status_update/lecture_cancel">
      @csrf
      @method('PUT')
      @if(isset($student_id))
        <input type="hidden" value="{{$student_id}}" name="student_id" />
      @endif
      <div class="row">
        <div class="col-12 col-lg-6 col-md-6 mb-1">
            <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action" confirm="休講依頼を送信しますか？">
              <i class="fa fa-envelope mr-1"></i>
              休講依頼を出す
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
