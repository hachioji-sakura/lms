@section('entry_form')
<div id="{{$domain}}_entry">
@if(!empty($result))
  <h4 class="bg-success p-3 text-sm">
    @if($result==='success')
      仮登録完了メールを送信しました。<br>
      送信したメールにて、24時間以内にユーザー登録を進めてください。<br>
    @elseif($result==='already')
      仮登録中の情報が残っています。<br>
      再送信したメールにて、24時間以内にユーザー登録を進めてください。
    @elseif($result==='exist')
      このメールはユーザー登録が完了しています。
    @endif
  </h4>
@else
<form method="POST"  action="/{{$domain}}/entry">    
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <div class="row">
      @component('students.forms.name', ['attributes' => $attributes, 'prefix'=>'']) @endcomponent
      @component('students.forms.email', [ 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
    </div>
    <div class="col-12">
      <h6 class="text-sm p-2 pl-2 mb-4" >
        入力いただいたメールアドレスに、<br>
        本登録用のURLを送信いたします。
      </h6>
    </div>
  </form>
</div>
<script>
$(function(){
  base.pageSettinged("{{$domain}}_entry", null);
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('{{$domain}}_entry')){
      $("form").submit();
    }
  });
});
</script>
@endif
@endsection
