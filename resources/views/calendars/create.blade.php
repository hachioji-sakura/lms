<div id="{{$domain}}_create">
  @if(isset($_edit))
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($student_id))
    <input type="hidden" value="{{$student_id}}" name="student_id" />
  @endif
  @if(isset($teacher_id))
    <input type="hidden" value="{{$teacher_id}}" name="teacher_id" />
  @endif
  @if(isset($manager_id))
    <input type="hidden" value="{{$manager_id}}" name="manager_id" />
  @endif

  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="title" class="w-100">
          生徒
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="student_user_id" class="form-control select2" width=100% placeholder="担当生徒" required="true">
          <option value="">(選択)</option>
          @foreach($students as $student)
             <option value="{{ $student->user_id }}" @if(isset($_edit) && $item['student_user_id'] == $student->user_id) selected @endif>{{$student->name()}}</option>
          @endforeach
        </select>
      </div>
    </div>
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
          @if(isset($item['start_date'])) value="{{$item['start_date']}}" >  @endif
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-8 col-md-8">
      <div class="form-group">
        <label for="start_hours" class="w-100">
          時刻
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-clock"></i></span>
          </div>
          <select name="start_hours" class="form-control float-left mr-1" required="true">
            @for ($i = 8; $i < 23; $i++)
              <option value="{{$i}}" @if($item['start_hours']==$i) selected @endif>{{str_pad($i, 2, 0, STR_PAD_LEFT)}}時</option>
            @endfor
          </select>
          <select name="start_minutes" class="form-control float-left mr-1" required="true">
            @for ($i = 0; $i < 6; $i++)
            <option value="{{$i*10}}"  @if($item['start_minutes']==$i*10) selected @endif>{{str_pad($i*10, 2, 0, STR_PAD_LEFT)}}分</option>>
            @endfor
          </select>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4 col-md-4">
      <div class="form-group">
        <label for="lesson_time" class="w-100">
          授業時間
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <select name="lesson_time" class="form-control" placeholder="授業時間" required="true">
          <option value="60" @if($item['lesson_time'] < 90) selected @endif>60分</option>
          <option value="90" @if($item['lesson_time'] >= 90) selected @endif>90分</option>
          <option value="120" @if($item['lesson_time'] >= 120) selected @endif>120分</option>
        </select>
      </div>
    </div>
  @if(count($teachers) > 0)
  <div class="col-12">
    <div class="form-group">
      <label for="title" class="w-100">
        講師
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="teacher_user_id" class="form-control" placeholder="担当講師" required="true">
        @foreach($teachers as $teacher)
           <option value="{{ $teacher->user_id }}" @if(isset($_edit) && $item['teacher_user_id'] == $teacher->user_id) selected @endif>{{$teacher->name()}}</option>
        @endforeach
      </select>
    </div>
  </div>
  @else
    @isset($item['teacher_user_id'])
    <input type="hidden" name="teacher_user_id" value="{{$item['teacher_user_id']}}" />
    @endisset
  @endif
  @component('lectures.select', ['attributes' => $attributes])
  @endcomponent
  <div class="col-12">
    <div class="form-group">
      <label for="place" class="w-100">
        場所
        <span class="right badge badge-danger ml-1">必須</span>
      </label>
      <select name="place" class="form-control" placeholder="場所" required="true">
        @foreach($attributes['place'] as $index => $name)
          <option value="{{ $index }}" @if(isset($_edit) && $item['place'] == $index) selected @endif>{{$name}}</option>
        @endforeach
      </select>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-12 col-lg-6 col-md-6 mb-1">
    <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
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
