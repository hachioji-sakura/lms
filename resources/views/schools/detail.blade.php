<div id="{{$domain}}_">
  <div class="col-12 my-1">
    {{-- 詳細表示項目を羅列する --}}
    @component('components.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
    @endcomponent
    @if($action === 'delete')
      @include('schools.component.form.delete_form', ['id' => $item['id']])
    @endif
    </div>
</div>
