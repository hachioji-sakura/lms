@component('components.list_filter', ['filter' => $filter, '_page' => $_page, '_line' => $_line, 'domain' => $domain, 'domain_name' => $domain_name, 'attributes'=>$attributes])
@slot("search_form")
<input type="hidden" name="view" value="{{$view}}">
@component('text_materials.forms.search_subjects', ['subjects' => $subjects]) @endcomponent
<div class="col-12 mb-2">
  <div class="form-group">
    <label for="search_keyword" class="w-100">
      {{__('labels.search_keyword')}}
    </label>
    <input type="text" name="search_keyword" class="form-control" placeholder="{{__('labels.search_keyword')}}"
    @if(isset($filter['search_keyword']))
    value="{{$filter['search_keyword']}}"
    @endif
    >
  </div>
</div>
<div class="col-12 mb-2">
  <div class="form-group">
    <label for="is_asc" class="w-100">
      {{__('labels.other')}}
    </label>
    <label class="mx-2">
      <input type="checkbox" value="1" name="is_publiced_only" class="icheck flat-green"
      @if(isset($filter['comment_filter']['is_publiced_only']) && $filter['comment_filter']['is_publiced_only']==1)
      checked
      @endif
      >{{__('labels.public')}}
    </label>
    <label class="mx-2">
      <input type="checkbox" value="1" name="is_unpubliced_only" class="icheck flat-green"
      @if(isset($filter['comment_filter']['is_unpubliced_only']) && $filter['comment_filter']['is_unpubliced_only']==1)
      checked
      @endif
      >{{__('labels.unpublic')}}
    </label>
  </div>
</div>
@endslot
@endcomponent
