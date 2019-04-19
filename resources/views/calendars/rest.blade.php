@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
    @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00'))
      {{-- 授業当日9時を過ぎたら休み連絡はできない
      <div class="col-12 col-lg-12 col-md-12 mb-1">
        <h4 class="text-danger">授業当日AM9:00以降の休み連絡はできません。</h4>
      </div>
      --}}
    @else
    @endif
    この授業予定をお休みしますか？
  @endslot
  @slot('forms')
  <div id="{{$domain}}_action">
    <form method="POST" action="/calendars/{{$item['id']}}/rest">
      @csrf
      @method('PUT')
      @if(isset($student_id))
        <input type="hidden" value="{{$student_id}}" name="student_id" />
      @endif

    <div class="row">
      @component('calendars.forms.rest_form', ['item' => $item, 'user'=>$user]) @endcomponent
      @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'rest', 'student_id' => $student_id]) @endcomponent
    </div>

    <div class="row">
      @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"].' 09:00:00'))
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              閉じる
          </button>
      </div>
      @else
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="{{$domain}}_action">
            <i class="fa fa-envelope mr-1"></i>
              休み連絡
          </button>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              閉じる
          </button>
      </div>
      @endif
    </div>
    </form>
  </div>
  @endslot
@endcomponent
