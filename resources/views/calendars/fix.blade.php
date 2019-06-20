@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
  この授業予定の確認連絡をしますか？
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
    @csrf
    @method('PUT')
    <div class="row">
      @component('calendars.forms.fix_form', ['item' => $item, 'user'=>$user]) @endcomponent
      @component('calendars.forms.target_member', ['item' => $item, 'user'=>$user, 'status'=>'fix', 'student_id' => $student_id]) @endcomponent
    </div>
    <div class="row">
    <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="この予定を確認済みに更新しますか？">
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

    </script>
  </form>
  @endslot
@endcomponent
