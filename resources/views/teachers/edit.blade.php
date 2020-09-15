@include($domain.'.create_form')
<div id="teachers_edit" class="direct-chat-msg">
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="student_parent_id" value="{{$student_parent_id}}">
    @yield('item_form')
    @if($user->role=="manager")
    @component('students.forms.editable_email', ['item' =>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
    @else
    @component('students.forms.email', ['item'=>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
    @endif
    @yield('bank_form')
    <div class="row">
      <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_edit">
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
