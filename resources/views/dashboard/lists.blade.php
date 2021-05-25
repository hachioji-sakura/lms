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
      {{--一括のボタン類はここに追加する--}}
      @if (isset($bulk_action['check_box']) && $bulk_action['check_box'] == true)
        <a href="javascript:void(0)" page_form="dialog" page_title="{{$bulk_action['label']}}{{__('labels.confirm')}}" page_url="{{$bulk_action['url']}}?" title="{{$bulk_action['label']}}" role="button" class="btn btn-primary btn-sm bulk_action_button ml-2" style="display: none;">
          <i class="fa fa-@isset($bulk_action['icon']){{$bulk_action['icon']}}@endisset  nav-icon"></i>
          {{$bulk_action['label']}}
        </a>
        <input type="hidden" name="action_url" value="{{$bulk_action['url']}}?">
      @endif
    </div>
    <div class="card-tools">
      @component('components.search_word', ['search_word' => $search_word])
      @endcomponent
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    @component('components.list', ['items' => $items, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name, 'bulk_action' => isset($bulk_action) ? $bulk_action : null])
    @endcomponent
  </div>
</div>
@yield('list_filter')
@endsection
