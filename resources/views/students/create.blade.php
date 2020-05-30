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
    </div>
  </form>
</div>

<script>
$(function(){
  base.pageSettinged("students_edit", []);
  lesson_checkbox_change($('input[name="lesson[]"]'));
  $('#students_edit').carousel({ interval : false});
  //submit
  $("button.btn-submit").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('students_edit .carousel-item.active')){
      $("form").submit();
    }
  });

});
</script>
