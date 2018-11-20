@section('profile')
@component('components.profile', ['item' => $item, 'user' => $user])
    @slot('courtesy')
    @endslot
    @slot('alias')
      <h6 class="widget-user-desc">
        <!--
          <i class="fa fa-calendar mr-1"></i>yyyy/mm/dd
          <br>
          <small class="badge badge-info mt-1">
            <i class="fa fa-user mr-1"></i>中学1年
          </small>
          <small class="badge badge-info mt-1">
            <i class="fa fa-chalkboard-teacher mr-1"></i>XXコース
          </small>
      -->
      </h6>
      {{--
        <div class="card-footer p-0">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a href="#comments" class="nav-link">
                コメント
                <span class="float-right badge bg-danger">99</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#events" class="nav-link">
                イベント
                <span class="float-right badge bg-danger">99</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#tasks" class="nav-link">
                タスク
                <span class="float-right badge bg-danger">99</span>
              </a>
            </li>
          </ul>
        </div>
      --}}
    @endslot
@endcomponent
@endsection
