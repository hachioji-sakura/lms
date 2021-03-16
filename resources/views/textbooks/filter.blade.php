<div class="col-4 mb-2 ml-2">
  <label for="search_word" class="w-100">
    {{__('labels.search_keyword')}}
  </label>
  <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
         @if(isset($filter['search_keyword']))
         value = "{{$filter['search_keyword']}}"
    @endif
  >
</div>
<div class="col-12 mb-2">
</div>
<div class="col-6 mb-2">
  @component('textbooks.forms.select_publisher', ['publishers'=> $item['publishers']]); @endcomponent
</div>
<div class="col-6 mb-2">
  @component('textbooks.forms.select_supplier', ['suppliers'=> $item['suppliers']]); @endcomponent
</div>
<div class="col-6 mb-2">
  @component('textbooks.forms.select_difficulty', []); @endcomponent
</div>
<div class="col-12 mb-2">
  @component('textbooks.forms.subject', ['subjects' => $item['subjects']]); @endcomponent
</div>
<div class="col-12 mb-2">
  @component('textbooks.forms.grade', ['grades' => $item['grades']]); @endcomponent
</div>
