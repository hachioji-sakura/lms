@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
  この依頼内容を差し戻しますか？
  @endslot
  @slot('forms')
    <div class="row">
    <div class="col-12 mb-1" id="commit_form">
      <form method="POST" action="/asks/{{$item['id']}}/status_update/cancel">
        @csrf
        @method('PUT')
        <button type="button" class="btn btn-submit btn-danger btn-block"  accesskey="commit_form">
          <i class="fa fa-check mr-1"></i>
          差戻
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
  @endslot
@endcomponent
