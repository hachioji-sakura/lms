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

@section('account_date_form')
<div class="col-6">
  <label for="start_date" class="w-100">
    {{__('labels.entiring')}}{{__('labels.day')}}
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
  </label>
  <div class="input-group">
    <input type="text" name="entry_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
    @if(isset($item) && !empty($item->entry_date))
    value = "{{date('Y/m/d', strtotime($item->entry_date))}}"
    @endif
    >
  </div>
</div>
@if($item->status=='unsubscribe')
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
