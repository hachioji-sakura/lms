@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item hr-1">
    <a href="/@yield('domain')/create?key={{$select_key}}" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>@yield('domain_name')登録
    </a>
  </li>
  <li class="nav-item has-treeview menu-open mt-2">
    <a href="#" class="nav-link">
      <i class="nav-icon fa fa-filter"></i>
      <p>
        フィルタリング
        <i class="right fa fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @foreach($keys as $index => $item)
      <li class="nav-item">
         <a href="/@yield('domain')?key={{$item['attribute_value']}}" class="nav-link @if($item['attribute_value']===$select_key) active @endif">
           <i class="fa fa-list-alt nav-icon"></i>{{$item['attribute_name']}}
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection
