@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  <div class="row">
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
            <small title="{{$item["id"]}}" class="badge badge-{{config('status_style')[$item['status']]}} mt-1 mr-1">{{$item[$key]}}</small>
          @elseif($key==='student_name')
            <label for="{{$key}}" class="w-100">
              {{$field['label']}}
            </label>
            @foreach($item->students as $member)
              <a target="_blank" href="/students/{{$member->user->details('students')->id}}" class="text-{{config('status_style')[$member->status]}}">
                @if($member->status==='new')
                <i class="fa fa-question-circle mr-1"></i>
                @elseif($member->status==='confirm')
                <i class="fa fa-question-circle mr-1"></i>
                @elseif($member->status==='fix')
                <i class="fa fa-fa-clock mr-1"></i>
                @elseif($member->status==='cancel')
                <i class="fa fa-ban mr-1"></i>
                @elseif($member->status==='presence')
                <i class="fa fa-check-circle mr-1"></i>
                @elseif($member->status==='absence')
                <i class="fa fa-calendar-times mr-1"></i>
                @elseif($member->status==='rest')
                <i class="fa fa-user-times mr-1"></i>
                @endif
                {{$member->user->details('students')->name}}
              </a>
              <br>
            @endforeach
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
            {{$field['label']}}
          </label>
          {{$item[$key]}}
          @endif
        </div>
    </div>
  @endforeach
  </div>
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    @if(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
