<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        @if(count($teachers)>1)
          <label for="title" class="w-100">
            担当講師
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <select name="teacher_id" class="form-control select2" width=100% placeholder="担当講師" required="true" onChange="teacher_id_change();">
            <option value="">(選択)</option>
            @foreach($teachers as $teacher)
               <option
               value="{{ $teacher->id }}"
               @if(isset($_edit) && $_edit==true && $item['teacher_id'] == $teacher->id) selected @endif
               >{{$teacher->name()}}</option>
            @endforeach
          </select>
        @elseif(count($teachers)==1)
          <label for="start_date" class="w-100">
            講師
          </label>
          <a href="/teachers/{{$teachers[0]->id}}" target="_blank">
          <i class="fa fa-user-tie mr-1"></i>
          {{$teachers[0]->name()}}
          </a>
          <input type="hidden" name="teacher_id" value="{{$teachers[0]->id}}" />
        @endif
      </div>
    </div>
    @component('student_groups.forms.select_student', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    <div class="col-12">
      <div class="form-group">
        <label for="title">
          グループ名
          <span class="right badge badge-danger ml-1">必須</span>
        </label>
        <input type="text" id="title" name="title" class="form-control" placeholder="例：幼児クラスA" required="true"
          @isset($item['title']) value="{{$item['title']}}" @endisset>
      </div>
    </div>
    @component('student_groups.forms.group_type', ['_edit'=>$_edit, 'item'=>$item, 'attributes' => $attributes]); @endcomponent
    <div class="col-12">
      <div class="form-group">
        <label for="howto" class="w-100">
          説明
          <span class="right badge badge-secondary ml-1">任意</span>
        </label>
        <textarea type="text" id="body" name="remark" class="form-control" placeholder="（このグループに関する説明など）" >@if(isset($_edit) && $_edit==true){{$item['remark']}}@endif</textarea>
      </div>
    </div>
  </div>
  <div class="row mt-2">
    <div class="col-12 col-lg-6 col-md-6 mb-1">
    <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
      @if(isset($_edit) && $_edit==true)
        更新する
      @else
        <i class="fa fa-plus-circle mr-1"></i>
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
