<div class="row">
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
          <label for="{{$key}}" class="w-100" title="{{$item["id"]}}">
            {{$field['label']}}
          </label>
          <small class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
          @elseif(isset($item[$key]) && gettype($item[$key])=='array')
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
            @foreach($item[$key] as $_item)
            <span class="text-xs mx-2">
              <small class="badge badge-primary mt-1 mr-1">
                {{$_item}}
              </small>
            </span>
            @endforeach
          @else
          <label for="{{$key}}" class="w-100">
            {{trim($field['label'],',')}}
          </label>
          @empty($item[$key])
            ー
          @else
            {!!nl2br($item[$key])!!}
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
