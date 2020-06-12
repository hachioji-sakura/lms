@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      ログイン情報
    </h5>
  </div>
  @component('students.forms.email', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @component('students.forms.password', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('bank_form')
@component('teachers.forms.bank_form', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes,]) @endcomponent
@endsection


@section('item_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      {{$domain_name}}情報
    </h5>
  </div>
  @component('students.forms.name', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  @component('students.forms.kana', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  <div class="col-12 mb-2">
    @component('components.select_birthday', ['_edit'=>$_edit, 'item'  => $item, 'prefix'=>''])
    @endcomponent
  </div>
  <div class="col-12 mb-2">
    @component('components.select_gender', ['_edit'=>$_edit, 'item'  => $item, 'prefix'=>''])
    @endcomponent
  </div>
  @component('students.forms.address', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'prefix'=>'',]) @endcomponent
  @component('students.forms.phoneno', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'prefix'=>'',]) @endcomponent
  @if(isset($user) && isset($user->role) && $user->role=="manager")
  @component('students.forms.editable_email', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @else
  @component('students.forms.email', ['_edit'=>$_edit, 'item' =>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @endif

</div>
@endsection

@section('lesson_week_form')
<div class="row">
  <div class="col-12">
    <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
      ※事務作業が可能な曜日・時間帯にチェックをいれてください
    </h6>
  </div>
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item' =>$item->user, 'prefix'=>'work', 'attributes' => $attributes, 'title' => '勤務可能な曜日・時間帯']) @endcomponent
</div>
@endsection
