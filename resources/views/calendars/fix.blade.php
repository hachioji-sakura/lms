@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
  @slot('page_message')
  この授業予定の確認連絡をしますか？
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
    @csrf
    @method('PUT')
    <div class="row">
      <div class="col-12 mb-1">
        <div class="form-group">
          <label for="status">
            この授業予定に出席する
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <div class="input-group">
            <div class="form-check">
                <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_fix" value="fix" required="true" onChange="status_radio_change()">
                <label class="form-check-label" for="status_fix">
                    はい
                </label>
            </div>
            <div class="form-check ml-2">
                <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_cancel" value="cancel" required="true"  onChange="status_radio_change()">
                <label class="form-check-label" for="status_cancel">
                    いいえ
                </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 collapse" id="cancel_reason">
      <div class="form-group">
        <label for="howto" class="w-100">
          授業予定に参加できない理由をお知らせください
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <textarea type="text" name="remark" class="form-control" placeholder="例：予定日時の都合があわなくなり、X月X日 15時～に変更したい。" required="true"></textarea>
      </div>
    </div>
    <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form">
          <i class="fa fa-envelope mr-1"></i>
          送信
        </button>
      </form>
    </div>
    <div class="col-12 col-lg-12 col-md-12 mb-1">
        <button type="reset" class="btn btn-secondary btn-block">
            閉じる
        </button>
    </div>
    <script>
    $(function(){
      base.pageSettinged("_form", null);
      //submit
      $("button.btn-submit").on('click', function(e){
        e.preventDefault();
        if(front.validateFormValue('_form')){
          $("form").submit();
        }
      });
    });

    function status_radio_change(obj){
      var is_cancel = $('input[type="radio"][name="status"][value="cancel"]').prop("checked");
      if(is_cancel){
        $("textarea[name='remark']").show();
        $("#cancel_reason").collapse("show");
      }
      else {
        $("textarea[name='remark']").hide();
        $("#cancel_reason").collapse("hide");
      }
    }

    </script>
  </form>
  @endslot
@endcomponent
