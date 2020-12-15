<div class="direct-chat-msg" id="ask_edit">
  <form id="edit" method="POST" action="/{{$domain}}/{{$item->id}}">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="target_model" value="{{$item->target_model}}" / >
    <input type="hidden" name="target_model_id" value="{{$item->target_model_id}}" / >
    <div class="row">
      @if($item->type=='unsubscribe')
      <div class="col-12">
        <label class="w-100">
          退会予定日
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          value = "{{date('Y/m/d', strtotime($item->start_date))}}"
        >
      </div>
      @elseif($item->type=='recess')
      <div class="col-6">
        <label class="w-100">
          休会開始日
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          value = "{{date('Y/m/d', strtotime($item->start_date))}}"
        >
      </div>
      <div class="col-6">
        <label class="w-100">
          休会終了日
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="text" id="end_date" name="end_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
          greater="start_date" greater_error="{{__('messages.validate_timezone_error')}}" not_equal="start_date" not_equal_error="{{__('messages.validate_timezone_error')}}"
          value = "{{date('Y/m/d', strtotime($item->end_date))}}"
        >
      </div>
      @endif
    </div>
    <div class="row mt-4">
      <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="ask_edit">
          <i class="fa fa-edit mr-1"></i>
          {{__('labels.update_button')}}
        </button>
      </div>
      <div class="col-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
      </div>
    </div>
  </form>
</div>
