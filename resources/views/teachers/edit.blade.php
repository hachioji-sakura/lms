@include($domain.'.create_form')
<div id="teachers_edit" class="direct-chat-msg">
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    @yield('item_form')
    <div class="row mb-2">
      @if($user->role=="manager")
      @component('students.forms.editable_email', ['item' =>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
      @yield('account_date_form')
      @else
        @component('students.forms.email', ['item'=>$item, 'attributes' => $attributes, 'is_label'=>true]) @endcomponent
      @endif
    </div>
    @yield('bank_form')
    <div class="row">
      <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="teachers_edit">
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
