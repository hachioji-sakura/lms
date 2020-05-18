<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open mt-2">
      <a href="#" class="nav-link">
        <i class="nav-icon fa fa-filter"></i>
        <p>
          {{__('labels.filter')}}
          <i class="right fa fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="/{{$domain}}/{{$target_user->id}}/tasks" class="nav-link">
            <i class="fa fa-tasks nav-icon"></i>{{__('labels.active')}}
          </a>
        </li>
        @foreach(config('attribute.task_status') as $key => $value)
        <li class="nav-item">
          <a href="/{{$domain}}/{{$target_user->id}}/tasks?search_status={{$key}}"  class="nav-link">
            <i class="fa fa-@if($key == 'new')plus @elseif($key == 'progress')play @elseif($key == 'done')stop @elseif($key == 'complete')pen @elseif($key == 'cancel')ban @endif mr-1"></i>{{$value}}
            @if(!empty($status_count[$key]))
            <span class="badge badge-{{config('status_style')[$key]}}">{{$status_count[$key]}}</span>
            @endif
          </a>
        </li>
        @endforeach
      </ul>
    </li>
  </li>
</ul>
