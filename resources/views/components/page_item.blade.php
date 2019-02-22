<div class="row"　id="page_item" >
@if(isset($field_logic))
  {{$field_logic}}
@else
  @foreach($fields as $key=>$field)
      @if(isset($field['size']))
        <div class="col-{{$field['size']}}">
      @else
        <div class="col-12">
      @endif
        <div class="form-group">
          @isset($field['format'])
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            {!! sprintf($field['format'], $item[$key]) !!}
          @else
            @if($key==="status_name")
              <label for="{{$key}}" class="w-100">
                {{$field['label']}}
              </label>
              <small class="badge badge-{{$item->status_style()}} mt-1 mr-1">{{$item[$key]}}</small>
            @else
              <label for="{{$key}}" class="w-100">
                {{trim($field['label'],',')}}
              </label>
              @empty($item[$key])
              ー
              @else
              {{$item[$key]}}
              @endempty
            @endif
          @endisset
        </div>
      </div>
      @isset($field['hr'])
      <div class="col-12">
        <hr>
      </div>
      @endisset
  @endforeach
@endif
</div>
@isset($add_form)
{{$add_form}}
@endisset
