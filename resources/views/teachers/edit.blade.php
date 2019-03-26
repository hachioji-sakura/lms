@include($domain.'.create_form')
<div class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}">
    @csrf
    @method('PUT')
    <div id="teachers_edit" class="carousel slide" data-ride="carousel" data-interval=false>
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('item_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('lesson_week_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          @yield('subject_form')
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="submit" class="btn btn-primary btn-block" accesskey="teachers_edit">
                  <i class="fa fa-edit mr-1"></i>
                    更新する
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){
  var form_data = util.getLocalData('teachers_edit');
  base.pageSettinged("teachers_edit", form_data);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('teachers_edit .carousel-item.active')){
      util.removeLocalData('teachers_edit');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('teachers_edit .carousel-item.active')){
      var form_data = front.getFormValue('teachers_edit');
      util.setLocalData('teachers_edit', form_data);
      $('#teachers_edit').carousel('next');
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#teachers_edit').carousel('prev');
  });
});
</script>
