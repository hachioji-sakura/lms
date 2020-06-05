<div class="card card-widget mb-2">
  <div class="card-header">
    <i class="fa fa-user-graduate mr-1"></i>生徒情報
  </div>
    @component('components.profile', ['item' => $item->student->user->details(), 'user' => $user, 'domain' => 'students', 'domain_name' => $domain_name])
      @slot('courtesy')
      @endslot
      @slot('alias')
        <h6 class="widget-user-desc">
          <small class="badge badge-primary mt-1 mr-1">
            {{$item->student->label_gender()}}
          </small>
          <small class="badge badge-primary mt-1 mr-1">
            {{$item->student->grade()}}
          </small>
          @if(!empty($item->student->school_name()))
          <small class="badge badge-primary mt-1 mr-1">
            {{$item->student->school_name()}}
          </small>
          @endif
        </h6>
      @endslot
    @endcomponent
</div>
