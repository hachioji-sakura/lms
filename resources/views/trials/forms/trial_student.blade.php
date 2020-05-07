<div class="card card-widget mb-2">
  <div class="card-header">
    <i class="fa fa-user-graduate mr-1"></i>生徒情報
  </div>
  @foreach($item->trial_students as $trial_student)
    @component('components.profile', ['item' => $trial_student->student->user->details(), 'user' => $user, 'domain' => $domain, 'domain_name' => $domain_name])
      @slot('courtesy')
      @endslot
      @slot('alias')
        <h6 class="widget-user-desc">
          <small class="badge badge-primary mt-1 mr-1">
            {{$trial_student->student->label_gender()}}
          </small>
          <small class="badge badge-primary mt-1 mr-1">
            {{$trial_student->student->grade()}}
          </small>
          @if(!empty($trial_student->student->school_name()))
          <small class="badge badge-primary mt-1 mr-1">
            {{$trial_student->student->school_name()}}
          </small>
          @endif
        </h6>
      @endslot
    @endcomponent
  @endforeach
</div>
