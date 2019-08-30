@if(count($items) > 0)
<table class="table table-hover">
  <tbody>
    <tr>
      @foreach($fields as $key => $field)
        <th>{{$field['label']}}</th>
      @endforeach
    </tr>
    @foreach($items as $i => $row)
      @component('components.list_item', ['row' => $row, 'index' => $i, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
      @endcomponent
    @endforeach
  </tbody>
</table>
@else
<div class="alert">
  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
</div>
@endif
