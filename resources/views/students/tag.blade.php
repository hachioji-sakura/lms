<div class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <div id="tag_edit" >
      <div class="row">
        @component('students.forms.student_type', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
      </div>
      <div class="row">
        <div class="col-12 mb-1">
          <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
            <i class="fa fa-times-circle mr-1"></i>
            キャンセル
          </a>
        </div>
        <div class="col-12 mb-1">
            <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="tag_edit">
              <i class="fa fa-edit mr-1"></i>
                {{__('labels.update_button')}}
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
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('tag_edit')){
      $("form").submit();
    }
  });
});
</script>
