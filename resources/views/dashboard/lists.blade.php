@section('list_pager')
@component('components.list_pager', ['_page' => $_page, '_maxpage' => $_maxpage, '_list_start' => $_list_start, '_list_end'=>$_list_end, '_list_count'=>$_list_count])
  @slot("addon_button")
  @endslot
@endcomponent
@endsection


@section('contents')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">@yield('title')</h3>
    <div class="card-title text-sm">
      @yield('list_pager')
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
    @endcomponent
  </div>
</div>
@yield('list_filter')
@endsection
