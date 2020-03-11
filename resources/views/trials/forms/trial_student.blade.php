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
            {{$trial_student->student->gender()}}
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


  @if(!empty($item->schedule_start_hope_date))
  @endif
  <div class="card-footer">
    <div class="row">
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">対応状況</h5>
          <span class="description-text">
            <small class="badge badge-{{config('status_style')[$item['status']]}} mx-2">
              {{$item["status_name"]}}
            </small>
          </span>
        </div>
      </div>
      <div class="col-sm-6 border-right">
        <div class="description-block">
          <h5 class="description-header">授業開始希望日</h5>
          <span class="description-text">
            @if(empty($item->schedule_start_hope_date))
             -
            @else
            <small class="badge badge-primary mx-2">
              {{$item["start_hope_date"]}}
            </small>
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>


</div>
