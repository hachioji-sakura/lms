@component('components.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
{{-- メッセージカスタマイズ --}}
  @slot('page_message')
    @if(isset($page_message) && !empty(trim($page_message)))
      {{$page_message}}
    @endif
  @endslot
  {{-- 表示部分カスタマイズ --}}
  @slot('field_logic')
  <div class="row">
  <div class="col-6">
    <div class="row">
    @foreach($fields as $key=>$field)
        @if(isset($field['size']))
        <div class="col-{{$field['size']}}">
        @else
        <div class="col-12">
        @endif
          <div class="form-group">
            @if($key==="place")
              <label for="{{$key}}" class="w-100">
                {{$field['label']}}
              </label>
              <small title="{{$item["id"]}}" class="badge badge-success mt-1 mr-1">{{$item->place()}}</small>
            @elseif($key==='student_name')
              <label for="{{$key}}" class="w-100">
                {{$field['label']}}
              </label>
              @foreach($item->students as $member)
                <a target="_blank" alt="student_name" href="/students/{{$member->user->details('students')->id}}" class="">
                  <i class="fa fa-user-graduate mr-1"></i>
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
  </div>
  <div class="col-6 border-left">
    @foreach($add_dates as $add_date => $_already_calendars)
      @if(count($_already_calendars)>0)
        <span>
          <i class="fa fa-calendar-check mr-1"></i>{{$add_date}}
          @foreach($_already_calendars as $_already_calendar)
          <span class="text-xs mx-2">
            <small class="badge badge-primary mt-1 mr-1">
              {{$_already_calendar->id}}
            </small>
          </span>
          @endforeach
        </span>
      @else
        <span>
          <i class="fa fa-calendar-plus mr-1"></i>{{$add_date}}
        </span>
      @endif
      <br>
    @endforeach
  </div>
</div>
  @endslot
  {{-- フォーム部分カスタマイズ --}}
  @slot('forms')
    @if(isset($forms) && !empty(trim($forms)))
      {{$forms}}
    @endif
  @endslot

@endcomponent
