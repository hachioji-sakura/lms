<div class="row">
  <div class="col-12">
    @component("components.list",['fields' => $fields, 'domain' => 'exam_results', 'domain_name' => __('labels.exam_results'), 'items' => $item->exam_results ])
    @endcomponent
  </div>
</div>
