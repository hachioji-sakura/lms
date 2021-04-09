@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        {{__('labels.filter')}}
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    @isset($item)
    <ul class="nav nav-treeview">
      @foreach(config('attribute.message_box') as $index => $name)
      <li class="nav-item">
         <a href="/{{$domain}}/{{$item->id}}/messages?search_list={{$index}}" class="nav-link @if(isset($search_list) && $index===$search_list) active @endif">
           {!!$name!!}
         </a>
       </li>
       @endforeach
    </ul>
    @endif
  </li>
</ul>
@endsection
