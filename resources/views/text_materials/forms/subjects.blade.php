<div class="col-12 mb-2">
  <label for="subjects" class="w-100">
    {{__('labels.subjects')}}
  </label>
  <div class="w-100">
    <select name="subject" class="form-control select2" width=100% placeholder="科目">
      <option value=" ">{{__('labels.selectable')}}</option>
      @foreach($subjects as $subject)
      <option value="{{$subject->id}}"
        {{isset($item) && $item->curriculums->count() >0 && $item->curriculums->first()->subjects->contains($subject->id)  ? "selected" : "" }}
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
    <select name="curriculum_ids[]" class="form-control select2" width="100%" placeholder="科目" multiple="multiple" >
    </select>
    @if(isset($item) && $item->curriculums->count() >0)
    @foreach($item->curriculums as $curriculum)
      <input type="hidden" name="__curriculum_ids[]" value="{{$curriculum->id}}" />
    @endforeach
    @endif
  </div>
</div>
<script>

$(function(){
  var form_id = $("form.filter").parent().attr('id');
  var __curriculum_ids = [];
  $("input[name='__curriculum_ids[]']").each(function(i){
    __curriculum_ids.push($(this).val());
  });
  $("select[name='subject']").on('change', function(e){
    var curriculum_form = $("select[name='curriculum_ids[]']");
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
              var selected = "";
              if(__curriculum_ids.indexOf(i+"") !== -1){
                selected = "selected";
              }
              _option_html+='<option value="'+i+'" '+selected+'>'+v+'</option>';
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

  $("select[name='subject']").change();

});

</script>
