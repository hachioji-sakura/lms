
<div class="col-12 mb-2">
  <label for="subjects" class="w-100">
    {{__('labels.subjects')}}
  </label>
  <div class="w-100">
    <select name="search_subject" class="form-control select2" width=100% placeholder="科目">
      <option value=" ">{{__('labels.selectable')}}</option>
      @foreach($subjects as $subject)
      <option value="{{$subject->id}}"
      @if(isset($filter['user_filter']['search_subject']) && in_array($subject->id, $filter['user_filter']['search_subject'])==true)
      selected
      @endif
      >{{$subject->name}}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="col-12 mb-2">
  <label for="curriculums" class="w-100">
    {{__('labels.curriculums')}}
  </label>
  <div class="w-100">
    <select name="search_curriculum[]" class="form-control select2" width="100%" placeholder="科目" multiple="multiple" >
    </select>
  </div>
</div>
<script>

$(function(){
  var form_id = $("form.filter").parent().attr('id');
  $("select[name='search_subject']").on('change', function(e){
    var curriculum_form = $("select[name='search_curriculum[]']");
    var _width = curriculum_form.attr("width");
    curriculum_form.select2('destroy');

    curriculum_form.empty();
    if(!util.isEmpty(this.value)){
      var url = "/api/curriculums";
      service.getAjax(false, url, {"search_subject_id" : this.value},
        function(result, st, xhr) {
          console.log(result);
          var options = {};
          if(result['status']===200){
            if(result["data"].length>0){
              for(var i=0,n=result["data"].length;i<n;i++){
                options[result["data"][i].id] = result["data"][i].name;
              }
            }
            var _option_html = "";
            $.each(options, function(i, v){
              _option_html+='<option value="'+i+'">'+v+'</option>';
            });
            curriculum_form.html(_option_html);
          }
        },
        function(xhr, st, err) {
          console.log(st);
          console.log(xhr);
            alert("UI取得エラー("+url+")");
        }, true
      );
    }
    curriculum_form.select2({
      width: _width,
      placeholder: '選択',
    });
  });
});

</script>
