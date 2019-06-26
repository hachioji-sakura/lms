@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action, 'user'=>$user])
  @slot('page_message')
  @endslot
  @slot('forms')
  <div id="_form">
  <form method="POST" action="/asks">
    @csrf
    <input type="hidden" name="parent_ask_id" value="{{$ask->id}}">
    <input type="hidden" name="type" value="teacher_change">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            代講を依頼する講師を選択してください
          </label>
          <select name="charge_user_id" class="form-control select2"  width=100% placeholder="講師" required="true" >
            <option value="">(選択)</option>
            @foreach($teachers as $teacher)
               <option
               value="{{ $teacher->user_id }}"
               >{{$teacher->name()}}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mb-1">
          <button type="button" class="btn btn-submit btn-info btn-block"  accesskey="_form" confirm="代講依頼を連絡しますか？">
            <i class="fa fa-envelope mr-1"></i>
            送信
          </button>
      </div>
      <div class="col-12 col-lg-12 col-md-12 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              閉じる
          </button>
      </div>

      <script>
      $(function(){
        base.pageSettinged("_form", null);
        //submit
      });
      </script>
    </div>
  </form>
  </div>
  @endslot
@endcomponent
