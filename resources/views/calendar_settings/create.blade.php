<div id="{{$domain}}_create">
  @if(isset($_edit))
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <div class="row">
    @component('calendar_settings.forms.course_type', ['item'=>$item, 'select_lesson' => $select_lesson, 'attributes' => $attributes]) @endcomponent
    @component('calendar_settings.forms.charge_subject', ['item'=>$item, 'select_lesson' => $select_lesson, 'candidate_teacher' => $candidate_teacher, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
    @component('calendar_settings.forms.lesson_place_floor', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
  </div>
  <div class="row">
    @component('calendar_settings.forms.lesson_week', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
    @component('calendar_settings.forms.select_time', ['item'=>$item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
    @component('calendar_settings.forms.course_minutes', ['item'=> $item, 'attributes' => $attributes, 'calendar'=>$item]) @endcomponent
  </div>

<div class="row">
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
      @if(isset($_edit))
        更新する
      @else
        登録する
      @endif
    </button>
    @if(isset($error_message))
      <span class="invalid-feedback d-block ml-2 " role="alert">
          <strong>{{$error_message}}</strong>
      </span>
    @endif
  </div>
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    <button type="reset" class="btn btn-secondary btn-block">
        キャンセル
    </button>
  </div>
</div>
</form>
</div>
