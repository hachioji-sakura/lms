<div id="{{$domain}}_create">
  @if(isset($_edit))
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  @if(isset($_page_origin))
    <input type="hidden" value="{{$_page_origin}}" name="_page_origin" />
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="start_date" class="w-100">
          日付
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          </div>
          <input type="text" id="start_date" name="start_date" class="form-control float-left" required="true" uitype="datepicker"
          @if(isset($item['start_date']))
           value="{{$item['start_date']}}" placeholder="(変更前) {{$item['start_date']}}">
          @else
            >
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="start_hours" class="w-100">
          時刻
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-clock"></i></span>
          </div>
          <input type="text" id="start_hours" name="start_hours" class="form-control float-left mr-1" required="true" list="_select_hours" autocomplete="off"
          @if(isset($item['start_hours']))
           value="{{$item['start_hours']}}" placeholder="(変更前) {{$item['start_hours']}}">
          @else
            >
          @endif
          <label class="float-left mt-2 mx-2">時</label>
          <input type="text" id="start_minutes" name="start_minutes" class="form-control float-left" required="true" list="_select_minutes" autocomplete="off"
          @if(isset($item['start_minutes']))
           value="{{$item['start_minutes']}}" placeholder="(変更前) {{$item['start_minutes']}}">
          @else
            >
          @endif
          <label class="float-left mt-2 mx-2">分</label>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="title" class="w-100">
          生徒
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="student_id" class="form-control" placeholder="担当生徒" required="true">
          @foreach($students as $student)
             <option value="{{ $student['id'] }}" @if(isset($_edit) && $item['student_id'] == $student['id']) selected @endif>{{$student['name']}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  @if(count($teachers) > 0)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="title" class="w-100">
          講師
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="teacher_id" class="form-control" placeholder="担当講師" required="true">
          @foreach($teachers as $teacher)
             <option value="{{ $teacher['id'] }}" @if(isset($_edit) && $item['teacher_id'] == $teacher['id']) selected @endif>{{$teacher['name']}}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="lecture_id" class="w-100">
          内容
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="lecture_id" class="form-control" placeholder="レッスン・コース・科目" required="true">
          @foreach($lectures as $lesson=>$lesson_item)
            <optgroup label="{{$lesson}}">
            @foreach($lesson_item as $course=>$course_item)
              @foreach($course_item as $subject=>$val)
                <option value="{{ $val }}" @if(isset($_edit) && $item['lecture_id'] == $val) selected @endif>{{$course.'>'.$subject}}</option>
              @endforeach
            @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="lesson_time" class="w-100">
          授業時間
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="lesson_time" class="form-control" placeholder="授業時間" required="true">
          <option value="60" @if($item['lesson_time'] <= 60) selected @endif>60分</option>
          <option value="90" @if($item['lesson_time'] <= 90) selected @endif>90分</option>
          <option value="120" @if($item['lesson_time'] >= 120) selected @endif>120分</option>
        </select>
      </div>
    </div>
  </div>

    <div class="row">
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="submit" class="btn btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit))
              更新する
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
      <div class="col-12 col-lg-6 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
