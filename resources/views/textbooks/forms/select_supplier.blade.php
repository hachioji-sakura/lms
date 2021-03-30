<div class="col-12">
  <div class="form-group">
    <label for='place_floor_id' class="w-100">
      {{__('labels.supplier_name')}}
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
      </div>
      <select name='supplier_id' class="form-control select2">
        <option value="">
          {{__('labels.selectable')}}
        </option>
        @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}"
          @if(isset($textbook->supplier->id) && $supplier->id == $textbook->supplier->id)
          selected
          @endif>
          {{$supplier->name}}
        </option>
        @endforeach
      </select>
    </div>
  </div>
</div>
