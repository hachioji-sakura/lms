@if($paginator->count() > 0)
  @if ($paginator->lastPage() > 0)
  <ul class="pagination pagination-sm m-0 float-right text-sm">
      @if( $paginator->currentPage() != 1)
      <li class="page-item">
          <a class="page-link" href="{{ $paginator->url(1) }}"><<</a>
       </li>
      <li class="page-item">
          <a class="page-link" href="{{ $paginator->url($paginator->currentPage()-1) }}">
              <span aria-hidden="true">&lt;</span>
              {{-- Previous --}}
          </a>
      </li>
      @endif
      <li class="page-item mx-2">{{$paginator->perPage()*($paginator->currentPage()-1)+1}} ～
        @if( $paginator->currentPage() == $paginator->lastPage())
        {{$paginator->perPage()*($paginator->currentPage()-1)+$paginator->count()}}
        @else
        {{$paginator->currentPage()*$paginator->perPage()}}
        @endif
         of {{$paginator->total()}}
      </li>
      @if($paginator->currentPage() != $paginator->lastPage())
      <li class="page-item">
          <a class="page-link" href="{{ $paginator->url($paginator->currentPage()+1) }}" >
              <span aria-hidden="true">&gt;</span>
              {{-- Next --}}
          </a>
      </li>
      <li class="page-item">
          <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">»</a>
      </li>
      @endif
      <li class="page-item">
        <a class="page-link btn btn-default btn-sm" data-toggle="modal" data-target="#filter_form" id="filter_button">
          <i class="fa fa-filter"></i>
          <span class="btn-label">{{__('labels.filter')}}</span>
        </a>
      </li>
  </ul>
  @endif
@endif
