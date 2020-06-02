<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="javascript:void(0)" page_form="dialog" page_title="{{__('labels.'.$domain).__('labels.add')}}" page_url="/{{$domain}}/create" title="{{__('labels.add_button')}}" role="button" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{__('labels.add_button')}}
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
    @foreach($subjects as $subject)
    <li class="nav-item">
      <a href="/{{$domain}}?search_subject_id={{$subject->id}}"  class="nav-link {{$search_subject_id == $subject->id ? 'active' : ''}}">
        {{$subject->name}}
      </a>
    </li>
    @endforeach
    </ul>
  </li>
</ul>
