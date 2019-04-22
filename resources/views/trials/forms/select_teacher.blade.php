@section('select_teacher_form')
  @if(count($candidate_teachers) > 0 && $select_lesson<1)
  @foreach($candidate_teachers as $lesson => $lesson_teachers)
  <ul class="mailbox-attachments clearfix row">
    <li class="col-12 bg-light" accesskey="" target="">
      <div class="row">
        <div class="col-12">
          {{$attributes['lesson'][$lesson]}}担当講師
        </div>
      </div>
    </li>
    @foreach($lesson_teachers as $teacher)
    <li class="col-6" accesskey="" target="">
      @component('trials.forms.charge_teacher', ['teacher' => $teacher,  'attributes' => $attributes, 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
        @slot('addon')
        <div class="col-12 mb-2">
          <a href="/{{$domain}}/{{$item->id}}/to_calendar?teacher_id={{$teacher->id}}&lesson={{$lesson}}" role="button" class="btn btn-block btn-info">担当講師にする　<i class="fa fa-chevron-right ml-2"></i></a>
        </div>
        @endslot
      @endcomponent
    </li>
    @endforeach
  </ul>
  @endforeach
  @else
  <div class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
  @endif
@endsection
