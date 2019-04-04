<div id="{{$domain}}_create">
  @if(isset($_edit))
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($student_id))
    <input type="hidden" value="{{$student_id}}" name="student_id" />
  @endif
  @if(isset($teacher_id))
    <input type="hidden" value="{{$teacher_id}}" name="teacher_id" />
  @endif
  @if(isset($manager_id))
    <input type="hidden" value="{{$manager_id}}" name="manager_id" />
  @endif

<div class="row">
  @component('calendars.forms.select_date', ['item'=>$item]);
  @endcomponent
  @component('calendars.forms.select_time', ['item'=>$item]);
  @endcomponent
  @component('calendars.forms.select_place', ['attributes' => $attributes])
  @endcomponent
  @component('calendars.forms.select_teacher', ['items'=>$teachers, 'item'=>$item]);
  @endcomponent
  @component('calendars.forms.select_student', ['items'=>$students]);
  @endcomponent
  @component('calendars.forms.select_exchanged_calendar', ['item'=>$item]);
  @endcomponent
</div>
<div class="row">
  @component('calendars.forms.select_lecture', ['attributes' => $attributes])
  @endcomponent
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
