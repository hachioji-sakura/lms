<a class="btn btn-default btn-sm mr-2 float-right" role="button" data-toggle="collapse" data-parent="#filter_form" href="#filter_form_item" class="" aria-expanded="true">
  <i class="fa fa-filter mr-1"></i>絞込
</a>
<ul class="pagination pagination-sm m-0 float-right">
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
</ul>
