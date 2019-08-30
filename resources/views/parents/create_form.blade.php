@section('parent_form')
@isset($parent)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    ご契約者様情報
  </div>
  @component('students.forms.name', ['_edit'=>$_edit, 'item' => $parent, 'prefix' => 'parent_']) @endcomponent
  @component('students.forms.kana', ['_edit'=>$_edit, 'item' => $parent, 'prefix' => 'parent_']) @endcomponent

  <div class="col-12 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="email" class="w-100">
        メールアドレス
      </label>
      <span>{{$parent->email}}</span>
    </div>
  </div>
  @component('students.forms.phoneno', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.address', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.password', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent

</div>
@endisset
@endsection
