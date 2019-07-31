<div id="{{$domain}}_create">
@if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
    @csrf
    <input type="hidden" name="title"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->title}}"
      @endif
    >
    <input type="hidden" name="lesson"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->lesson}}"
      @endif
    >
    <input type="hidden" name="course_type"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->course_type}}"
      @endif
    >
    <input type="hidden" name="course_minutes"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->course_minutes}}"
      @endif
    >
    <input type="hidden" name="subject"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->subject}}"
      @endif
    >
    <input type="hidden" name="teacher_id"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->teacher_id}}"
      @endif
    >
    <input type="hidden" name="student_id"
      @if(isset($_edit) && $_edit==true)
        value="{{$item->student_id}}"
      @else
        value="{{$student_id}}"
      @endif
    >
    @if(isset($student_id) && $student_id>0)
      @if($student->is_juken()==true)
        <input type="hidden" name="is_juken" value="1">
      @else
        <input type="hidden" name="is_juken" value="0">
      @endif
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="title" class="w-100">
            生徒
          </label>
          <span>
            {{$student->name()}}
          </span>
        </div>
      </div>
      <div class="col-6">
        <div class="form-group">
          <label for="title" class="w-100">
            学年
          </label>
          <span>
            {{$student->grade()}}
          </span>
          <input type="hidden" name="grade"
            @if(isset($_edit) && $_edit==true)
              value="{{$item->grade}}"
            @else
              value="{{$student->tag_value('grade')}}"
            @endif
          >
        </div>
      </div>
      <div class="col-12 col-lg-3 mt-1">
        <div class="form-group">
          <label for="lesson_week_count" class="w-100">
            通塾回数/週
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="lesson_week_count" name="lesson_week_count" class="form-control w-50 float-left" required="true" maxlength=1 inputtype="numeric"
           minvalue="1"
           onChange="calc_tuition()"
          @if(isset($_edit) && $_edit==true)
           value="{{$item['lesson_week_count']}}" placeholder="(変更前) {{$item['lesson_week_count']}}">
          @else
           >
          @endif
          <span class="ml-2 float-left">回/週</span>
        </div>
      </div>
      <div class="col-12 col-lg-6 mt-1">
        <div class="form-group">
          <label for="title" class="w-100">
            授業
            @if(!isset($_edit) || $_edit!=true)
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            @endif
          </label>
          @if(isset($_edit) && $_edit==true)
          <span>
            {{$item->title}}
          </span>
          @else
            <select name="calendar_setting_id" class="form-control select2"  width=100% required="true"
              onChange="calc_tuition()"
            >
              <option value="">{{__('labels.selectable')}}</option>
              <?php
              $is_set = [];
              ?>
            @foreach($calendar_settings as $schedule_method => $d1)
              @foreach($d1 as $lesson_week => $settings)
                @foreach($settings as $setting)
                <?php
                $setting = $setting->details();
                $set_key = $setting->get_tag_value('lesson').'_'.$setting->get_tag_value('course_type').'_'.$setting->user->details()->id.'_'.$setting->get_tag_value('kids_lesson');
                ?>
                @if(isset($is_set[$set_key]) && $is_set[$set_key]==true)
                  @continue
                @endif
                <option
                  value="{{$setting->id}}"
                  lesson="{{$setting->get_tag_value('lesson')}}"
                  course_type="{{$setting->get_tag_value('course_type')}}"
                  course_minutes="{{$setting->get_tag_value('course_minutes')}}"
                  teacher_id="{{$setting->user->details()->id}}"
                  @if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true)
                  subject="{{$setting->get_tag_value('subject')}}"
                  @elseif($setting->get_tag_value('lesson')==4)
                  subject="{{$setting->get_tag_value('kids_lesson')}}"
                  @endif
                >{{$setting["title"]}}</option>
                <?php
                $is_set[$set_key] = true;
                ?>
                @endforeach
              @endforeach
            @endforeach
            </select>
          @endif
        </div>
      </div>
      <div class="col-12 col-lg-3 mt-1">
        <div class="form-group">
          <label for="tuition" class="w-100">
            受講料
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="tuition" name="tuition" class="form-control w-50 float-left" required="true" maxlength=5 inputtype="numeric"
           minvalue="1000"
          @if(isset($_edit) && $_edit==true)
           value="{{$item['tuition']}}" placeholder="(変更前) {{$item['tuition']}}"
          @endif
          >
          <span class="ml-2 float-left">円 / 時間</span>
        </div>
      </div>
      @component('tuitions.forms.calc_script', []) @endcomponent
    @endif
    </div>
    <div class="row mt-2">
      <div class="col-12">
        <div class="form-group">
          <label for="remark" class="w-100">
            備考
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <textarea type="text" id="remark" name="remark" class="form-control" rows=3>@if(isset($_edit) && $_edit==true){{$item['remark']}}@endif</textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-2">
        <label for="start_date" class="w-100">
          {{__('labels.setting')}}{{__('labels.duration')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <div class="input-group">
          <input type="text" id="enable_start_date" name="start_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
          @if(isset($_edit) && $_edit==true && $item['start_date']!='9999-12-31')
          value = "{{date('Y/m/d', strtotime($item['start_date']))}}"
          @endif
          >
          <span class="float-left mx-2 mt-2">～</span>
          <input type="text" id="enable_end_date" name="end_date" class="form-control float-left w-30" uitype="datepicker" placeholder="例：2000/01/01"
          @if(isset($_edit) && $_edit==true && $item['end_date']!='9999-12-31')
          value = "{{date('Y/m/d', strtotime($item['end_date']))}}"
          @endif
          >
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-lg-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              登録する
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-lg-6 col-lg-6 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
