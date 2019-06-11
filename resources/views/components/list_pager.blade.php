@isset($addon_button)
{{$addon_button}}
@endisset
<ul class="pagination pagination-sm m-0 float-right text-sm">
  @if($_maxpage>=1)
    @if($_page > 1)
    <li class="page-item"><a class="page-link" href="javascript:void(0);" accesskey="pager" page="1">«</a></li>
    <li class="page-item"><a class="page-link" href="javascript:void(0);" accesskey="pager" page="{{$_page-1}}">&lt;</a></li>
    @endif
    <li class="page-item mx-2">{{$_list_start}}～{{$_list_end}}件 / {{$_list_count}}件中</li>
    @if($_page+1 <= $_maxpage)
    <li class="page-item"><a class="page-link" href="javascript:void(0);" accesskey="pager" page="{{$_page+1}}">&gt;</a></li>
    <li class="page-item"><a class="page-link" href="javascript:void(0);" accesskey="pager" page="{{$_maxpage}}">»</a></li>
    @endif
  @endif
  <li class="page-item">
    <a class="page-link btn btn-float btn-default btn-sm" data-toggle="modal" data-target="#filter_form">
      <i class="fa fa-filter"></i>
      <span class="btn-label">絞込</span>
    </a>
  </li>
</ul>
