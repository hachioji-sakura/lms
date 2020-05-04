@if(isset($_edit) && $_edit==true && $item->is_group()==false)
@else
  <div class="col-12 course_type_selected collapse">
    <div class="form-group">
      <label for="title" class="w-100">
        {{__('labels.student_groups')}}
      </label>
      <select name="student_group_id" class="form-control" width=100% placeholder="生徒グループ" onChange="select_student_group_change()">
        <option value="">{{__('labels.selectable')}}</option>
      </select>
    </div>
  </div>
  <script>
  $(function(){
    //get_charge_students();
  });
  var _student_group = {};
  function select_student_group_change(){
    var select_form = $("select[name='student_group_id']");
    var student_group_id = select_form.val();
    var select_student = _student_group[student_group_id];
    var student_id_form = $("select[name='student_id[]']");
    student_id_form.val(select_student).trigger('change');
  }
  function get_student_group(){
    var teacher_id = ($('*[name=teacher_id]').val())|0;
    var course_type = $('input[type="radio"][name="course_type"]:checked').val();
    if(util.isEmpty(course_type)) return;
    if(course_type=="single"){
      return;
    }
    console.log("get_student_group:"+teacher_id+":"+course_type);
    //対象の生徒を取得
    service.getAjax(false, '/api_student_groups/'+teacher_id+"?search_type="+course_type, null,
      function(result, st, xhr) {
        if(result['status']===200){
          var c = 0;
          var select_form = $("select[name='student_group_id']");
          select_form.empty();
          select_form.append('<option value="">{{__('labels.selectable')}}</option>');
          $.each(result['data'], function(id, val){
            _student_group[val["id"]] =[];
            $.each(val["students"],function(i, s){
              _student_group[val["id"]].push(s["id"]);
            });
            var _option = '<option value="'+val['id']+'"';
            _option+= '>'+val['title']+'</option>';
            select_form.append(_option);
            c++;
          });
          console.log(_student_group);
          var _width = select_form.attr("width");
          select_form.val([]);
        }
      },
      function(xhr, st, err) {
          alert("UI取得エラー(api_student_groups)");
      }
    );
  }

  </script>
@endif
