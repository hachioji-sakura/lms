<div class="input-group mb-3">
  <input type="text" name="search_word" class="form-control" placeholder="{{__('labels.search_keyword')}}" value="{{urlencode($search_word)}}" style="width:140px;" accesskey="keyword_search">
  <span class="input-group-append">
    <button type="button" class="btn btn-info btn-flat" id="search_button">
      <i class="fa fa-search"></i>
    </button>
  </span>
  @if(isset($is_filter_button) && $is_filter_button==true)
    <a class="ml-1 page-link btn btn-default btn-sm" data-toggle="modal" data-target="#filter_form" id="filter_button">
    <i class="fa fa-filter"></i>
    <span class="btn-label">{{__('labels.filter')}}</span>
    </a>
  @endif
</div>
