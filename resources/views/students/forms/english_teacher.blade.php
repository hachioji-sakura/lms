<div class="col-12 english_talk_form">
  <div class="form-group">
    <label for="english_teacher" class="w-100">
      @if(isset($_teacher) && $_teacher===true)
      以下を選択してください。
      @else
      英会話講師のご希望はございますか？
      @endif
      <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      @if(isset($_teacher) && $_teacher===true)
      <h6 class="text-sm p-1 pl-2 mt-2 bg-warning" >
        ※英会話ご希望の生徒に必要な項目となります。
      </h6>
      @endif
    </label>
    @foreach($attributes['english_teacher'] as $index => $name)
    @if(isset($_teacher) && $_teacher===true && $index==='both')
    @else
      <label class="mx-2">
        <input type="radio" id="english_teacher_{{$index}}" value="{{ $index }}" name="english_teacher" class="icheck flat-green" required="true"
        @if($_edit===true && isset($item) && $item->has_tag('english_teacher', $index)===true)
        checked
        @endif
        >{{$name}}
      </label>
    @endif
    @endforeach
  </div>
</div>
