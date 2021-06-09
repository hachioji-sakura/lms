<div class="row mt-2">
  <div class="col-12 mb-2">
    <label>{{__('labels.subjects')}}</label>
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    <select name="subject_id" id="select_subject" width="100%" class="form-control select2">
      <option value=" ">{{__('labels.selectable')}}</option>
      @foreach($subjects as $subject)
      <option value="{{$subject->id}}"
      @if(!empty($item) && $_edit)
        {{$item->curriculums->count() >0 && $item->curriculums->first()->subjects->contains($subject->id)  ? "selected" : "" }}
      @endif
      >
      {{$subject->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="col-12" id="curriculums">

  </div>
  <div class="col-12" id="new_curriculums">

  </div>
</div>
<script>

$(function(){
  var grade = $('input[name="grade"]').val();
  var form_id = $("form#create_task_form").attr('id');
  if( util.isEmpty(grade) ){
    var school = 'none';
  }else{
    var school = grade.match(/^./)[0];
  }
  var lesson = $('input[name="has_english_lesson"]').val();
  var lesson_count = $('input[name="lesson_count"]').val();
  if( lesson == "true" && lesson_count == 1){
    $('select#select_subject option').each(function(){
      if($(this).text().match("選択") != null ){
        return true;
      }else if(lesson == true && $(this).text().match("英会話") != null){
        return true;
      }else{
        $(this).remove();
      }
    });
  }else{
    $('select#select_subject option').each(function(){
      if($(this).text().match("選択") != null ){
        return true;
      }else if(lesson == true && $(this).text().match("英会話") != null){
        return true;
      }else if(school == "e" && $(this).text().match("小学") == null){
        $(this).remove();
      }else if(school == "j" && $(this).text().match("中学") == null){
        $(this).remove();
      }else if(school == "h" &&  ($(this).text().match("中学") != null   || $(this).text().match("小学") != null ||$(this).text().match("英会話") != null )){
        $(this).remove();
      }else{
        //当てはまらなければ残す
        return true;
      }
    });
  }
  @if($_edit && $item->curriculums->count() > 0)
  $('#curriculums').load( "{{url('/curriculums/get_select_list')}}?subject_id="+$('select#select_subject').val()
  +"&task_id={{$item->id}}", function(){
    base.pageSettinged(form_id);
  });
  @endif

  $("#select_subject").on('change', function(e){
    if(this.value == ' '){
      console.log(this.value);
      $("#curriculums").empty();
    }else{
      $('#curriculums').load( "{{url('/curriculums/get_select_list')}}?subject_id="+$('select#select_subject').val(),function(){
        base.pageSettinged(form_id);
      });
    }
  });
});

</script>
