@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
  この依頼内容を承認しますか？
  @endslot
  @slot('forms')
  <form method="POST" action="/calendars/{{$item['id']}}" id="_form">
    @csrf
    @method('PUT')
    <div class="row">
    <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form">
          <i class="fa fa-check mr-1"></i>
          承認
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
