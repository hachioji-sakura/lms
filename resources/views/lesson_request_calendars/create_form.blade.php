@section('first_form')
<div class="row">
  {{--
    @component('calendars.forms.select_teacher', ['_edit'=>$_edit, 'teachers'=>$teachers]); @endcomponent
  --}}
    @component('calendars.forms.select_date', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_place', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    @component('calendars.forms.select_time', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    <div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
      <div class="form-group">
        <label for="remark" class="w-100">
        {{__('labels.remark')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：ミーティング" >@if($_edit==true){{$item->remark}}@endif</textarea>
      </div>
    </div>
</div>
@endsection
@section('second_form')
<div class="row">
  @if(isset($teachers) && count($teachers)==1)
    @component('calendars.forms.select_exchanged_calendar', ['_edit' => $_edit, 'item'=>$item, 'attributes' => $attributes]);  @endcomponent
    @component('lesson_request_calendars.forms.charge_subject', ['_edit'=>$_edit, 'item'=>$item, 'teacher'=>$item->teacher, 'attributes' => $attributes]); @endcomponent
  @endif
  <div class="col-12">
    <div class="form-group">
      <label for="remark" class="w-100">
      {{__('labels.remark')}}
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      </label>
      <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：ミーティング" >@if($_edit==true){{$item->remark}}@endif</textarea>
    </div>
  </div>
</div>
@endsection
