@section('list_pager')
<div class="card-title text-sm">
  {{$items->appends(Request::query())->links('components.paginate', [ ])}}
</div>
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
