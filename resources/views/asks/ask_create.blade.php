<div class="direct-chat-msg">
  @if(isset($_edit) && $_edit==true)
    <form id="edit" method="POST" action="/{{$domain}}/{{$item->id}}/ask/{{$ask->id}}">
      @method('PUT')
  @else
    <form id="edit" method="POST" action="/{{$domain}}/{{$item->id}}/ask">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="charge_user_id" value="1" / >
    <input type="hidden" name="type" value="{{$ask_type}}">
    <div id="ask_entry" class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          {{---
          <div class="col-12">
            <div class="form-group">
              <label class="w-100">
                依頼種別
                @if(!isset($_edit) || $_edit==false)
                <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
                @endif
              </label>
              @if(isset($_edit) && $_edit==true)
                {{$ask->type_name()}}
              @else
                @foreach($attributes['ask_type'] as $index => $name)
                <label class="mx-2" for="rest_type_1">
                  <input type="radio" value="{{$index}}" name="type" class="icheck flat-green" required="true">
                  {{$name}}
                </label>
                @endforeach
              @endif
            </div>
          </div>
          ---}}
          @if(isset($_edit) && $_edit==true)
          <div class="col-12">
            <div class="form-group">
              <label class="w-100">
                ステータス
              </label>
              <span class="text-xs">
                  <small class="badge badge-{{config('status_style')[$ask->status]}} mt-1 mr-1">
                    {{$ask->status_name()}}
                </small>
              </span>
            </div>
          </div>
          @endif
          <div class="col-12">
            <div class="form-group">
              <label for="body" class="w-100">
                内容
                <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
              </label>
              <textarea type="text" id="body" name="body" required="true" class="form-control" style="height:240px;" placeholder="例：毎週水曜日 19:00～20:00の予定を、金曜日 18:00～19:00に変更したい" >@if(isset($_edit) && $_edit==true){{$ask->body}}@endif</textarea>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                {{__('labels.cancel_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1 btn-confirm">
                <i class="fa fa-file-alt mr-1"></i>
                {{__('labels.confirm_button')}}
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item" id="confirm_form">
          <div class="row">
            <div class="col-12 font-weight-bold" >依頼種別</div>
            <div class="col-12 p-3">
              @if(isset($_edit) && $_edit==true)
                {{$ask->type_name()}}
              @else
              <span id="type_name"></span>
              @endif
            </div>
            <div class="col-12 font-weight-bold" >内容</div>
            <div class="col-12 p-3"><span id="body"></span></div>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                {{__('labels.back_button')}}
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_create"
                  @if(isset($_edit) && $_edit==true)
                  confirm="{{__('messages.confirm_update')}}">
                    {{__('labels.update_button')}}
                  @else
                  confirm="{{__('messages.confirm_add')}}">
                    {{__('labels.create_button')}}
                  @endif
                    <i class="fa fa-caret-right ml-1"></i>
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
  var form_data = null;
  base.pageSettinged("ask_entry", form_data);
  $('#ask_entry').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('ask_entry .carousel-item.active')){
      $(this).prop("disabled",true);
      $("#edit").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    var form_data = front.getFormValue('ask_entry');
    if(front.validateFormValue('ask_entry .carousel-item.active')){
      $('body, html').scrollTop(0);
      $('#ask_entry').carousel('next');
      $('#ask_entry').carousel({ interval : false});
      $("ol.step li").removeClass("is-current");

      if($(this).hasClass('btn-confirm')){
        base.pageSettinged("confirm_form", form_data_adjust(form_data));
        $("ol.step #step_confirm").addClass("is-current");
      }
      else {
        $("ol.step #step_input").addClass("is-current");
      }
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('body, html').scrollTop(0);
    $('#ask_entry').carousel('prev');
    $('#ask_entry').carousel({ interval : false});
  });
  //確認画面用のパラメータ調整
  function form_data_adjust(form_data){
    console.log(form_data);
    var _names = ["type"];
    $.each(_names, function(index, value) {
      form_data[value+"_name"] = "";
      if(form_data[value+'']){
        $("input[name='"+value+"']:checked").each(function() {
          form_data[value+"_name"] += $(this).parent().parent().text().trim()+'<br>';
        });
      }
    });

    return form_data;
  }
});
</script>
