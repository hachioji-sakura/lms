<div id="{{$domain}}_create">
  <form method="POST" action="
  @if($_reply == false)
  /messages/create
  @else
  /messages/{{$item->id}}/reply
  @endif
  " enctype="multipart/form-data">
    @csrf
    <div class="row">
      <div class ="col-12">
        <label>To</label>
        @if($_reply == false)
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <select name="target_user_id[]" class="form-control select2" width="100%" multiple="multiple">
          <option value=" "></option>
          @foreach($charge_users as $target_user)
            <option value="{{$target_user->user_id}}">{{$target_user->name_last}} {{$target_user->name_first}}</option>
          @endforeach
        </select>
        @else
          @if($item->create_user_id == $user->user_id)
          {{$item->target_user->details()->name()}}
          <input type="hidden" name="target_user_id[]" value="{{$item->target_user_id}}">
          @else
          {{$item->create_user->details()->name()}}
          <input type="hidden" name="target_user_id[]" value="{{$item->create_user_id}}">
          @endif
        @endif
      </div>
      <div class="col-12 mt-2">
         <div class="form-group">
           <label>{{__('labels.message_title')}}</label>
           @if($_reply == false)
           <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
           <input type="text" name="title" class="form-control" placeholder="タイトル" required="true" maxlength="50" >
           @else
           Re:{{$item->title}}
           <input type="hidden" name="title" value="Re:{{$item->title}}">
           @endif
         </div>
      </div>
      @if($_reply)
        <div class="col-12">
          <label for="original_message" class="w-100">
            {{__('labels.original_message')}}
             <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#original_message"><i class="fas fa-plus"></i></button>
          </label>
        </div>
        <div class="collapse" id="original_message">
          <div class="col-12">
            {!!nl2br($item->body)!!}
          </div>
        </div>
      @endif
      <input type="hidden" name="type" value="information">
      <!--
      種別は用途が曖昧なためいったん出さない
      <div class ="col-6">
        <label>{{__('labels.message_type')}}</label>
        @if($_reply == false)
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <select name="type" class="form-control">
          @foreach($message_type as $key => $value)
            <option value="{{$key}}">{{$value}}</option>
          @endforeach
        </select>
        @else
        {{config('attribute.message_type')[$item->type]}}
        <input type="hidden" name="type" value="{{$item->type}}">
        @endif
      </div>
    -->
      <div class="col-12 mt-2">
        <div class="form-group">
          <label>{{__('labels.body')}}</label>
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          <textarea class="form-control" name="body" rows="5"  required="true" maxlength="5000"></textarea>
          @if($_reply == false)
          <input type="hidden" name="parent_message_id" value="0">
          @else
          <input type="hidden" name="parent_message_id" value="
          {{$item->parent_message_id == 0 ? $item->id : $item->parent_message_id}}
          ">
          @endif
        </div>
      </div>
      <div class="col-12 mb-2">
        <input type="file" name="upload_file" class="form-control" placeholder="{{__('labels.file')}}">
      </div>
    </div>
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <button class="btn btn-submit btn-primary form-control" accesskey="{{$domain}}_create">
            <i class="fa fa-envelope mr-1"></i>{{__('labels.send_button')}}
          </button>
        </div>
      </div>
      <div class="col-6">
        <button type="reset" class="btn btn-secondary btn-block">
          {{__('labels.cancel')}}
        </button>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){

  @if($_reply)
    var dataId = '{{$domain}}_create_{{$item->id}}';
  @else
    var dataId = '{{$domain}}_create';
  @endif
  var form_data = util.getLocalData(dataId);
  if( form_data ){
    dom.confirmMessage('{{__('labels.confirm')}}','{{__('messages.message_restore_contents')}}', function(){
        base.pageSettinged("{{$domain}}_create",form_data);
        if($("select[name='target_user_id[]']").length>0){
          var target_user_id_form = $("select[name='target_user_id[]']");
          var _width = target_user_id_form.attr("width");
          target_user_id_form.select2('destroy');
          target_user_id_form.select2({
            width: _width,
            placeholder:' {{__("labels.selectable")}}',
          });
        }
    });
  }
  save_input_form(dataId,form_data);
});

function save_input_form(dataId, form_data){
  timerId = setTimeout(function(){
    var form_data = front.getFormValue('{{$domain}}_create');
    delete form_data['_token'];
    util.setLocalData(dataId,form_data);
    if($('#subDialog').is(':visible')){
      save_input_form(dataId, form_data);
    }
  },600000);//10分おき
}


</script>
