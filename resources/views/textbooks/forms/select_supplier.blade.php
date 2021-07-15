@empty(!$suppliers)
<div class="col-12 col-md-6">
  <div class="form-group">
    <label for='{{$prefix}}supplier_id' class="w-100">
      {{__('labels.supplier_name')}}
      @if($prefix !=='search_')
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
      @endif
    </label>
    <div class="input-group">
      <select name='{{$prefix}}supplier_id' class="form-control select2" width="100%">
        <option value=" ">{{__('labels.selectable')}}</option>
        @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}"
            @if(request()->search_supplier_id == $supplier->id)
            selected
            @endif
            @if(isset($textbook->supplier->id) && $supplier->id == $textbook->supplier->id)
            selected
            @endif
          >
            {{$supplier->name}}
          </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
@endempty
