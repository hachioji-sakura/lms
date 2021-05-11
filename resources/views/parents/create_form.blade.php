@section('parent_form')
@isset($parent)
<div class="row">
  <div class="col-12 bg-info p-2 pl-4 mb-4">
    <i class="fa fa-user-friends mr-1"></i>
    ご契約者様情報
  </div>
  @component('students.forms.name', ['_edit'=>$_edit, 'item' => $parent, 'prefix' => 'parent_']) @endcomponent
  @component('students.forms.kana', ['_edit'=>$_edit, 'item' => $parent, 'prefix' => 'parent_']) @endcomponent
  @if($user->role=="manager")
  @component('students.forms.editable_email', ['_edit'=>$_edit, 'item' => $parent, 'prefix' => 'parent_']) @endcomponent
  @else
  <div class="col-12 col-md-6">
    <div class="form-group">
      <label for="email" class="w-100">
        メールアドレス
      </label>
      <span>{{$parent->email}}</span>
    </div>
  </div>
  @endif
  @component('students.forms.phoneno', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.address', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent
  @if($_edit==false)
  @component('students.forms.password', ['_edit'=>$_edit, 'item' => $parent, 'attributes' => $attributes]) @endcomponent
  @endif
</div>
@endisset
@endsection
@section('account_date_form')
<div class="col-6">
  <label for="start_date" class="w-100">
    {{__('labels.join')}}{{__('labels.day')}}
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="entry_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && !empty($item->entry_date))
    value = "{{date('Y/m/d', strtotime($item->entry_date))}}"
    @else
    value = "{{date('Y/m/d')}}"
    @endif
    >
  </div>
</div>
@if(isset($item) && ($item->status=='unsubscribe' || !empty($item->unsubscribe_date)))
<div class="col-6">
  <label for="unsubscribe_date" class="w-100">
    {{__('labels.unsubscribe')}}{{__('labels.day')}}
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="unsubscribe_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && !empty($item->unsubscribe_date))
    value = "{{date('Y/m/d', strtotime($item->unsubscribe_date))}}"
    @endif
    >
  </div>
</div>
@endif
@endsection
