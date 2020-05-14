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
            <i class="fa fa-tasks nav-icon"></i>{{__('labels.all')}}
            @if($status_count['all'] > 0)
            <span class="badge badge-primary">{{$status_count['all']}}</span>
            @endif
          </a>
        </li>
        @foreach(config('attribute.task_status') as $key => $value)
        <li class="nav-item">
          <a href="/{{$domain}}/{{$target_user->id}}/tasks?search_status={{$key}}"  class="nav-link">
            <i class="fa fa-{{config('attribute.status_icon')[$key]}} nav-icon"></i>{{$value}}
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
