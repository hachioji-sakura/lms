@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
    @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"]).' 09:00:00')
      {{-- 授業当日9時を過ぎたら休み連絡はできない --}}
      <div class="col-12 col-lg-12 col-md-12 mb-1">
        <h4 class="text-danger">授業当日AM9:00以降の休み連絡はできません。</h4>
      </div>
    @else
      この授業予定を休みにしますか？
    @endif
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}/rest">
    @csrf
    @method('PUT')
    @if(isset($student_id))
      <input type="hidden" value="{{$student_id}}" name="student_id" />
    @endif
    @if(isset($_page_origin))
      <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
    @endif
  <div class="row">
    @if(strtotime(date('Y/m/d H:i:s')) >= strtotime($item["date"]).' 09:00:00')
    <div class="col-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            閉じる
        </button>
    </div>
    @else
    <div class="col-12 col-lg-6 col-md-6 mb-1">
        <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_delete">
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
  @endslot
@endcomponent
