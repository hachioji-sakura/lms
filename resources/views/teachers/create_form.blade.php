@section('account_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-key mr-1"></i>
      {{__('labels.login')}} {{__('labels.info')}}
    </h5>
  </div>
  @component('students.forms.email', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
  @component('students.forms.password', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('item_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-friends mr-1"></i>
      {{__('labels.teacher_setting')}}
    </h5>
  </div>
  @component('students.forms.name', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  @component('students.forms.kana', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
  <div class="col-12 mb-2">
    @component('components.select_birthday', ['_edit'=>$_edit, 'item' => $item, 'prefix'=>''])
    @endcomponent
  </div>
  <div class="col-12 mb-2">
    @component('components.select_gender', ['_edit'=>$_edit, 'item' => $item, 'prefix'=>''])
    @endcomponent
  </div>
  @component('students.forms.address', ['_edit'=>$_edit, 'item' => $item, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.phoneno', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes, 'prefix'=>'',]) @endcomponent
  @component('teachers.forms.select_locale', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes])@endcomponent

</div>
@endsection

@section('charge_form')
<div class="row">
  @component('students.forms.lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, 'prefix'=>'', 'title'=> __('labels.charge_lesson')]) @endcomponent
  @component('students.forms.lesson_place', ['title'=>__('labels.workable_classroom'), '_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes]) @endcomponent
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item->user, 'prefix'=> 'lesson', 'attributes' => $attributes, 'title' => __('labels.lesson_week_time')]) @endcomponent
</div>
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="schedule_remark" class="w-100">
        {{__('labels.schedule_remark')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea type="text" name="schedule_remark" class="form-control" placeholder="{{__('labels.schedule_remark_placeholder')}}" maxlength=200>{{$item->get_tag_value('schedule_remark')}}</textarea>
    </div>
  </div>
</div>
@endsection

@section('lesson_week_form')
<div class="row">
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item->user, 'prefix'=> 'trial', 'attributes' => $attributes, 'title' => __('labels.trial_week_time')]) @endcomponent
</div>
<div class="row subject_form">
  @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item->user, 'prefix'=> 'season_lesson', 'attributes' => $attributes, 'title' => __('labels.season_lesson_week_time'), 'from_time_index' => '11_12', 'to_time_index'=> '17_18']) @endcomponent
  <div class="col-12">
    <div class="alert alert-warning text-sm">
      <i class="icon fa fa-exclamation-triangle"></i>
      {!!nl2br(__('messages.info_season_lesson_week_time'))!!}
    </div>
  </div>
</div>
@endsection


@section('subject_form')
<div class="row">
  @component('students.forms.subject', ['_edit'=>$_edit, 'item'=>$item->user, '_teacher' => true, 'attributes' => $attributes, 'category_display' => false, 'grade_display' => true]) @endcomponent
  @component('students.forms.english_teacher', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.english_talk_lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.piano_level', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
  @component('students.forms.kids_lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => true,]) @endcomponent
</div>
@endsection

@section('bank_form')
@component('teachers.forms.bank_form', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes,]) @endcomponent
@endsection

@section('tag_form')
<div class="row">
  @component('teachers.forms.teacher_character', ['_edit'=>$_edit, 'item'=>$item,'attributes' => $attributes]) @endcomponent
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
<div class="col-6">
  <label for="start_date" class="w-100">
    {{__('labels.retirement')}}{{__('labels.day')}}
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
@endsection
