<div class="direct-chat-msg">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-1 pl-2 mb-4">
          <i class="fa fa-user-graduate mr-1"></i>
          {{$item->name}}さんの登録
        </h5>
      </div>
    </div>
    <form method="POST" id="create_login_info" action="/{{$domain}}/{{$item->id}}/create_login_info">
      @csrf
      @method('PUT')
      <div class="row">
        @if($item->user->status == 1)
          @include($domain.'.forms.student_id')
          @include($domain.'.forms.password')
        @elseif($item->user->status == 0)
          @include($domain.'.forms.student_id', ['_edit' => true])
        @else
          {{__('labels.no_data')}}
        @endif
      </div>
      <div class="row">
        <div class="col-12">
          <button type="button" form="create_login_info" class="btn btn-submit btn-primary btn-block" accesskey="create_login_info"
            @if($item->user->status == 1)
            ><i class="fa fa-key mr-1"></i>{{__('labels.add_button')}}
            @elseif($item->user->status == 0)
            ><i class="fa fa-key mr-1"></i>{{__('labels.edit')}}
            @else
            disabled><i class="fa fa-key mr-1"></i>{{__('labels.edit')}}
            @endif
          </button>
        </div>
      </div>
    </form>

    @if($item->user->status == 0)
    <div class="row">
      <div class="col-12">
        <form method="POST" id="reset_login_info" action="/{{$domain}}/{{$item->id}}/create_login_info">
          @csrf
          @method('PUT')
          <input type="hidden" name="reset" value="true">
          <button type="button" form="reset_login_info" class="btn btn-submit btn-secondary btn-block mt-2 mb-2" confirm="ID・パスワードをリセットしてよろしいですか？" accesskey="reset_login_info"><i class="fa fa-trash-alt mr-1" ></i>{{__('labels.reset')}}</button>
        </form>
      </div>
    </div>
    @endif
  </div>
</div>

<script>

  $(function(){
    $('button.btn-submit[form="create_login_info"]').on('click', function(e){
      e.preventDefault();
      if(front.validateFormValue('create_login_info')){
        $(this).prop("disabled",true);
        $("form#create_login_info").submit();
      }
    });
    $('button.btn-submit[form="reset_login_info"]').on('click', function(e){
      e.preventDefault();
      if(front.validateFormValue('reset_login_info')){
        $(this).prop("disabled",true);
        $("form#reset_login_info").submit();
      }
    });
  })

</script>
