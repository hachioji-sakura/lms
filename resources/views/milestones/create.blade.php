<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
  @if(isset($origin))
    <input type="hidden" value="{{$origin}}" name="origin" />
  @endif
  @if(isset($item_id))
    <input type="hidden" value="{{$item_id}}" name="item_id" />
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
            種別
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <select name="type" class="form-control" placeholder="種別" required="true">
            @foreach($attributes['milestone_type'] as $index => $name)
               <option value="{{ $index }}"
               @if(isset($_edit)  && $_edit==true && $item['type'] == $index)
               selected
               @endif
               >{{$name}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            概要
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="title" name="title" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item['title']}}" placeholder="(変更前) {{$item['title']}}">
          @else
           placeholder="50文字まで">
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            目標詳細
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <textarea type="text" id="body" name="body" class="form-control" required="true"  maxlength=500
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item['body']}}" >{{$item['body']}}</textarea>
          @else
            placeholder="500文字まで" ></textarea>
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
              登録する
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
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
<script>
$(function(){
  base.pageSettinged('{{$domain}}_create', null);
});
</script>
