<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
　  <div class="row">
      <div class="col-12">
        <label for="charge_subject" class="w-100">
          イベント種別
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <div class="">
          <select name="event_type_id" class="form-control select2" width=100% >
{{--
@foreach($attributes['work'] as $index=>$name)
<option value="{{$index}}"
@if(isset($filter['calendar_filter']['search_work']) && in_array($index, $filter['calendar_filter']['search_work'])==true)
selected
@endif
>{{$name}}</option>
@endforeach
--}}
          </select>
        </div>
      </div>
　  </div>


    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            名称
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="title" name="title" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item['title']}}" placeholder="(変更前) {{$item['title']}}">
          @else
           placeholder="">
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 mt-2">
        <div class="form-group">
          <label for="lesson_place_floor" class="w-100">
            送信対象
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="place_floor_id" class="form-control" width="100%" required="true" >
            <option value="">{{__('labels.selectable')}}</option>
            @foreach( as $place)
              @foreach($place->floors as $floor)
              @if(isset($item["tagdata"]) && isset($item["tagdata"]["lesson_place"]) && isset($item["tagdata"]["lesson_place"][$place->id]))
                <option value="{{$floor->id}}"
                  @if(isset($calendar) && $calendar->place_floor_id==$floor->id)
                    selected
                  @elseif(isset($item->place_floor_id) && $item->place_floor_id==$floor->id)
                    selected
                  @elseif($loop->index==0)
                    selected
                  @endif
                >{{$floor->name()}}</option>
                @endif
              @endforeach
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="event_to_date" class="w-100">
            開催期間(終)
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="event_to_date" name="event_to_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['event_to_date']) && $item['event_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_to_date']))}}"
            @elseif(isset($item) && isset($item['event_to_date']) && $item['event_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_to_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="response_from_date" class="w-100">
            回答期間(始)
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="response_from_date" name="response_from_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['response_from_date']) && $item['response_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_from_date']))}}"
            @elseif(isset($item) && isset($item['response_from_date']) && $item['response_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_from_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="response_to_date" class="w-100">
            回答期間(終)
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="response_to_date" name="response_to_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['response_to_date']) && $item['response_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_to_date']))}}"
            @elseif(isset($item) && isset($item['response_to_date']) && $item['response_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_to_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>
    </div>


    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            備考
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" name="description" class="form-control"  maxlength=1000
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item->description}}" >{{$item->description}}</textarea>
          @else
            placeholder="1000文字まで" ></textarea>
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              {{__('labels.add_button')}}
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.cancel_button')}}
          </button>
      </div>
    </div>
  </form>
</div>
