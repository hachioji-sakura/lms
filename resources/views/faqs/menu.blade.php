
@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  @if(isset($user) && $user->role=='manager')
  <li class="nav-item hr-1">
    <a href="/{{$domain}}/create" class="nav-link">
      <i class="fa fa-plus nav-icon"></i>{{__('labels.faqs')}}{{__('labels.add')}}
    </a>
  </li>
  @endif
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
         <a href="/{{$domain}}" class="nav-link @if(!isset($search_type)) active @endif">
           <i class="fa fa-list-alt nav-icon"></i>すべて
         </a>
       </li>
       @foreach($attributes['faq_type'] as $index => $name)
      <li class="nav-item">
        @if($index=="teacher")
          @if(!isset($user) || ($user->role!=="teacher" && $user->role!=="manager"))
            @continue
          @endif
        @elseif($index=="manager")
          @if(!isset($user) || $user->role!=="manager")
            @continue
          @endif
        @endif
         <a href="/{{$domain}}?search_type={{$index}}" class="nav-link @if(isset($search_type) && $index===$search_type) active @endif">
           <i class="fa fa-list-alt nav-icon"></i>{{$name}}
         </a>
       </li>
       @endforeach
    </ul>
  </li>
</ul>
@endsection
@section('page_footer')
@if(isset($user) && $user->role=='manager')
<dt>
  <a class="btn btn-app"  href="javascript:void(0);" page_title="{{__('labels.faqs')}}{{__('labels.add')}}" page_form="dialog" page_url="{{$domain}}/create">
    <i class="fa fa-plus"></i>{{__('labels.faqs')}}{{__('labels.add')}}
  </a>
</dt>
@endif
@endsection
