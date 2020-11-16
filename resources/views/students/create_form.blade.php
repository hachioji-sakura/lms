@section('item_form')
<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-user-graduate mr-1"></i>
      {{__('labels.students')}}
      {{__('labels.setting')}}
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
  @component('students.forms.school', ['_edit'=>$_edit, 'item' => $item, 'prefix'=>'','attributes' => $attributes]) @endcomponent
</div>
@endsection

@section('lesson_week_form')
<div class="row">
  @isset($item->user)
    @component('students.forms.lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, 'prefix'=>'']) @endcomponent
    @component('students.forms.work_time', ['_edit'=>$_edit, 'item'=>$item->user, 'prefix'=> 'lesson', 'attributes' => $attributes, 'title' => 'ご希望の通塾曜日・時間帯']) @endcomponent
  <div class="col-12">
    <div class="form-group">
      <label for="schedule_remark" class="w-100">
        {{__('labels.schedule_remark')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea type="text" name="schedule_remark" class="form-control" placeholder="{{__('labels.schedule_remark_placeholder')}}" maxlength=200>{{$item->get_tag_value('schedule_remark')}}</textarea>
    </div>
  </div>
  @endisset
</div>
@endsection

@section('subject_form')
<div class="row">
  @isset($item->user)
    @component('students.forms.subject', ['_edit'=>$_edit, 'item'=>$item->user, '_teacher' => false, 'attributes' => $attributes, 'category_display' => false, 'grade_display' => false]) @endcomponent
    @component('students.forms.english_teacher', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => false,]) @endcomponent
    @component('students.forms.english_talk_lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => false,]) @endcomponent
    @component('students.forms.piano_level', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => false,]) @endcomponent
    @component('students.forms.kids_lesson', ['_edit'=>$_edit, 'item'=>$item->user, 'attributes' => $attributes, '_teacher' => false,]) @endcomponent
  @endisset
</div>
@endsection
