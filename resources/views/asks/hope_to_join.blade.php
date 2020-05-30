@extends('layouts.loginbox')
@section('title')
  ご入会希望に関する連絡
@endsection
@section('title_header')@yield('title')@endsection
@section('content')
@if($item->status=='new')
<div id="ask_hope_to_join">
  <form method="POST" action="/asks/{{$item['id']}}/status_update/commit">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @method('PUT')
    <input type="hidden" name="key" value="{{$access_key}}" />
    <section class="content-header">
    	<div class="container-fluid">
        <div class="row mb-4">
          <div class="col-12">
            <div class="form-group">
              <label for="status" class="w-100">
                {{(__('messages.message_hope_to_join'))}}
                <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
              </label>
              <div class="input-group">
                <div class="form-check">
                    <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_commit" value="commit" required="true" onChange="status_radio_change()">
                    <label class="form-check-label" for="status_commit">
                        {{__('labels.yes')}}
                    </label>
                </div>
                <div class="form-check ml-2">
                    <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_cancel" value="cancel" required="true"  onChange="status_radio_change()">
                    <label class="form-check-label" for="status_cancel">
                      {{__('labels.no')}}
                    </label>
                </div>
              </div>
            </div>
          </div>
          <script>
          function status_radio_change(){
            var is_cancel = $('input[type="radio"][name="status"][value="cancel"]').prop("checked");
            if(is_cancel){
              console.log("status_radio_change:hide");
              $("#schedule_start_hope_date").collapse("hide");
            }
            else {
              console.log("status_radio_change:show");
              $("#schedule_start_hope_date").collapse("show");
            }
          }
          </script>
        </div>
        <div class="row collapse" id="schedule_start_hope_date">
          <?php
          $trial = $item->get_target_model_data();
          ?>
          <div class="col-12 mb-2">
              <label for="start_date" class="w-100">
                {{__('labels.schedule_start_hope_date')}}
                <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
              </label>
              <div class="input-group">
                <input type="text" name="schedule_start_hope_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}" minvalue="{{date('Y/m/d')}}"
                @if(isset($trial) && !empty($trial->schedule_start_hope_date))
                  value ="{{date('Y/m/d', strtotime($trial->schedule_start_hope_date))}}"
                @endif
                >
              </div>
          </div>
          @component('students.forms.lesson_week_count', ['_edit'=>true, 'item'=>$trial, 'attributes' => $attributes]) @endcomponent
          @component('students.forms.course_minutes', ['_edit'=>true, 'item'=>$trial, '_teacher' => false, 'attributes' => $attributes]) @endcomponent
          @component('students.forms.work_time', ['_edit'=>true, 'item'=>$trial, 'prefix' => 'lesson', 'attributes' => $attributes, 'title' => 'ご希望の通塾曜日・時間帯']) @endcomponent
        </div>
    		<div class="row">
    			<div class="col-12 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="ask_hope_to_join" confirm="">
    					<i class="fa fa-envelope mr-1"></i>
    					{{__('labels.send_button')}}
    				</button>
    			</div>
    		</div>
    	</div>
    </section>
  </form>
</div>
<script>
$(function(){
  base.pageSettinged("ask_hope_to_join", null);
  status_radio_change();
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    var _confirm = $(this).attr("confirm");
    if(!util.isEmpty(_confirm)){
      if(!confirm(_confirm)) return false;
    }
    if(front.validateFormValue('ask_hope_to_join')){
      $("form").submit();
    }
  });
});
</script>
@elseif($item->status=='commit')
  <h4 class="bg-success p-3 text-sm">
    ご入会希望のご連絡を頂き、大変感謝致します。<br>
<br>
    改めて、通塾スケジュールについて、<br>
    ご連絡をいたしますので、お待ちください。
  </h4>
@elseif($item->status=='cancel')
<h4 class="bg-success p-3 text-sm">
  この度はご連絡いただき、誠にありがとうございました。<br>
  <br>
  ご入会キャンセルの件、承知しました。<br>
  <br>
  また、生徒様の学習方法・進学について、<br>
  お困りごとがありましたら、いつでも相談にのりますので、<br>
  ご気軽にご連絡ください。<br>
  <br>
  どうぞよろしくお願い申し上げます。
</h4>
@endif
@endsection
