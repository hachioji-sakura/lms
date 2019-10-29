@section('contents')
<div class="card">
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body table-responsive p-0">
  @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
  @endcomponent
</div>
</div>
@endsection
