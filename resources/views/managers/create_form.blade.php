@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </h5>
  </div>
  @component('students.forms.email', ['item'=>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @component('students.forms.password', ['item'=>$item, 'attributes' => $attributes]) @endcomponent
</div>
@endsection



@section('item_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      {{$domain_name}}情報
    </h5>
  </div>
  @component('students.forms.name', ['item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  @component('students.forms.kana', ['item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  <div class="col-12 mb-2">
    @component('components.select_birthday', ['item' => $item])
    @endcomponent
  </div>
  <div class="col-12 mb-2">
    @component('components.select_gender', ['item' => $item])
    @endcomponent
  </div>
  @component('students.forms.phoneno', ['item'=>$item, 'attributes' => $attributes, 'prefix'=>'',]) @endcomponent

</div>
@endsection

@section('lesson_week_form')
<div class="row">
  @component('students.forms.work_time', ['item'=>$item, 'prefix'=>'work', 'attributes' => $attributes]) @endcomponent
</div>
@endsection
