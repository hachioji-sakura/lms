@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  @foreach($fields as $key=>$field)
      @if(isset($field['size']))
      <div class="col-{{$field['size']}}">
      @else
      <div class="col-12">
      @endif
        <div class="form-group">
          @if($key==="status_name")
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @if($item['status']==="presence")
              <small class="badge badge-success mt-1 mr-1">{{$item[$key]}}</small>
            @elseif($item['status']==="absence" || $item['status']==="rest")
              <small class="badge badge-danger mt-1 mr-1">{{$item[$key]}}</small>
            @elseif($item['status']==="fix")
              <small class="badge badge-primary mt-1 mr-1">{{$item[$key]}}</small>
            @elseif($item['status']==="confirm")
              <small class="badge badge-warning mt-1 mr-1">{{$item[$key]}}</small>
            @else
              <small class="badge badge-secondary mt-1 mr-1">{{$item[$key]}}</small>
            @endif
          @elseif($key==="student_name")
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item->members as $member)
              @if($member->user->details()->role==="student")
                {{$member->user->details()->name}}
                <a target="_blank" href="/students/{{$member->user->details()->id}}" class="badge badge-primary ml-1">
                  <i class="fa fa-arrow-right"></i>
                </a>
              @endif
            @endforeach
          @else
          <label for="{{$key}}" class="w-100">
            {{$field['label']}}
          </label>
          {{$item[$key]}}
          @endif
        </div>
    </div>
  @endforeach
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    @if(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
