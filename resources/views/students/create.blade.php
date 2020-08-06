@include($domain.'.create_form')
<div id="students_edit" class="direct-chat-msg">
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <input type="hidden" name="student_parent_id" value="{{$student_parent_id}}">
    @yield('item_form')
    <div class="row">
      <div class="col-12 mb-1">
        <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="students_edit">
          @if($_edit==true)
          <i class="fa fa-edit mr-1"></i>
          {{__('labels.update_button')}}
          @else
          <i class="fa fa-plus mr-1"></i>
          {{__('labels.add_button')}}
          @endif
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
