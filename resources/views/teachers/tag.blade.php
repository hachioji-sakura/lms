@include($domain.'.create_form')
<div class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @csrf
    @method('PUT')
    <div id="tag_edit" >
      @yield('tag_form')
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-times-circle mr-1"></i>
            キャンセル
          </a>
        </div>
        <div class="col-12 mb-1">
            <button type="submit" class="btn btn-primary btn-block" accesskey="tag_edit">
              <i class="fa fa-edit mr-1"></i>
                更新する
            </button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){
  base.pageSettinged("tag_edit", null);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('tag_edit')){
      $("form").submit();
    }
  });
});
</script>
