<div id="create_login_info" class="direct-chat-msg">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h5 class="bg-info p-1 pl-2 mb-4">
          <i class="fa fa-user-graduate mr-1"></i>
          {{$item->name}}さんの登録
        </h5>
      </div>
    </div>
    <form method="POST"  action="/{{$domain}}/{{$item->id}}/create_login_info">
      @csrf
      @method('PUT')
      @if($item->user->status == 1)
        @include($domain.'.forms.student_id')
        @include($domain.'.forms.password')
      @elseif($item->user->status == 0)
        @include($domain.'.forms.student_id', ['_edit' => true])
      @else
        {{__('labels.no_data')}}
      @endif
      <div class="col-12">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="create_login_info"
          @if($item->user->status == 1)
          ><i class="fa fa-key mr-1"></i>{{__('labels.add_button')}}
          @elseif($item->user->status == 0)
          ><i class="fa fa-key mr-1"></i>{{__('labels.edit')}}
          @else
          disabled><i class="fa fa-key mr-1"></i>{{__('labels.edit')}}
          @endif
        </button>
      </div>
    </form>
  </div>
</div>
<script>
  $(function(){
    $("button.btn-submit").on('click', function(e){
      e.preventDefault();
      if(front.validateFormValue('create_login_info')){
        $("form").submit();
      }
    });
  })
</script>
