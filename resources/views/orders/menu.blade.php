<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="javascript:void(0);" page_title="{{__(('labels.orders'))}}{{__('labels.add')}}" page_form="dialog" page_url="/orders/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{__('labels.orders')}}{{__('labels.add')}}
    </a>
  </li>

</ul>
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
          <a href="/orders" class="nav-link {{!request()->has('search_status') ? 'active': ''}}">
            <i class="fa fa-list-alt nav-icon"></i>
            {{__('labels.all')}}
          </a>
        </li>
        @foreach(config("attribute.order_status") as $key => $value)
        <li class="nav-item">
          <a href="/orders?search_status={{$key}}" class="nav-link {{request()->search_status == $key ? 'active': ''}}">
            <i class="fa fa-{{config('attribute.order_status_icon')[$key]}} nav-icon"></i>
            {{$value}}
          </a>
        </li>
        @endforeach
      </ul>
    </li>
  </li>
</ul>
