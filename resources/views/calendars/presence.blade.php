@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
    @if(strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
      以下の授業予定の出欠をつけてください
    @else
      <div class="col-12 col-lg-12 col-md-12 mb-1">
        <h4 class="text-danger">出欠確認は、授業開始15分前から行ってください。</h4>
      </div>
    @endif
  @endslot
  @slot('forms')
  @method('PUT')
  <div class="row">
    @if(strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
      {{-- 当日開始15分前～終了15分後までの表示 --}}
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <form method="POST" action="/calendars/{{$item['id']}}/presence">
          @csrf
          @if(isset($student_id))
            <input type="hidden" value="{{$student_id}}" name="student_id" />
          @endif
          @if(isset($_page_origin))
            <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
          @endif
          @method('PUT')
          <button type="submit" class="btn btn-success btn-block"  accesskey="{{$domain}}_delete">
              <i class="fa fa-check-circle mr-1"></i>
              出席
          </button>
        </form>
      </div>
      <div class="col-12 col-lg-6 col-md-6 mb-1">
        <form method="POST" action="/calendars/{{$item['id']}}/absence">
          @csrf
          @if(isset($_page_origin))
            <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
          @endif
          @method('PUT')
          <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_delete">
            <i class="fa fa-times-circle mr-1"></i>
              欠席
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
