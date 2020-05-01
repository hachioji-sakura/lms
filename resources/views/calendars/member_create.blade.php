@component('calendars.page', ['item' => $item, 'fields' => $fields, 'action'=>$action, 'domain' => $domain, 'user'=>$user])
  @slot('page_message')
    <div class="col-12 bg-warning p-2 mb-2">
      <i class="fa fa-exclamation-triangle mr-1"></i>
      カレンダーに生徒を追加します。
    </div>
  @endslot
  @slot('forms')
  <div id="{{$domain}}_member_create">
    <form method="POST" action="/calendars/{{$item['id']}}/members">
      @csrf
      <input type="text" name="dummy" style="display:none;" / >
      <input type="hidden" name="teacher_id" value="{{$item->user->details('teachers')->id}}" / >
      <div class="row" id="member_list">
        <div class="col-12">
          <div class="form-group">
            <label for="title" class="w-100">
              {{__('labels.students')}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <select name="student_id[]" class="form-control select2" multiple="multiple" placeholder="{{__('labels.charge_student')}}" required="true" width="100%">
              <option value="">{{__('labels.selectable')}}</option>
            </select>
              @foreach($item->get_students() as $member)
                <input type="hidden" name="select_student_id[]"
                  value="{{$member->user->details('students')->id}}"
                  grade="{{$member->user->details('students')->tag_value('grade')}}"
                  >
              @endforeach
          </div>
          <div id="select_student_none" class="alert">
            <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
          </div>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label for="to_status" class="w-100">
              {{__('labels.updated_status')}}
              <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
            </label>
            <div class="input-group" >
              <input class="form-check-input icheck flat-grey" type="radio" name="to_status" id="to_status_fix" value="fix" required="true" checked>
              <label class="form-check-label mr-3" for="to_status_fix" checked>
                {{__('labels.not_require_student_confirm')}}
              </label>
              <input class="form-check-input icheck flat-red" type="radio" name="to_status" id="to_status_confirm" value="confirm" required="true" >
              <label class="form-check-label mr-3" for="to_status_confirm">
                {{__('labels.require_student_confirm')}}
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_member_create" {{__('labels.close_button')}}
              confirm="{{__('messages.confirm_update')}}">
              <i class="fa fa-check-circle mr-1"></i>
              {{__('labels.add_button')}}
          </button>
        </div>
        <div class="col-12 col-md-6 mb-1" id="{{$domain}}_member_create">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
        </div>
      </div>
    </form>
  </div>
  <script>
  $(function(){
    base.pageSettinged("{{$domain}}_member_create", []);
    get_charge_students();
  });
  function get_charge_students(){
    var teacher_id = ($('*[name=teacher_id]').val())|0;
    var lesson = ($('input[name=lesson]').val())|0;
    //対象の生徒を取得
    var select_student_id_form = $("input[name='select_student_id[]']");
    var select_student_ids = [];
    if(select_student_id_form.length > 0){
      select_student_id_form.each(function(index, element){
        select_student_ids.push(($(element).val()|0));
      });
    }
    service.getAjax(false, '/teachers/'+teacher_id+'/students?lesson='+lesson, null,
      function(result, st, xhr) {
        if(result['status']===200){
          var c = 0;
          var student_id_form = $("select[name='student_id[]']");
          student_id_form.empty();
          student_id_form.select2('destroy');
          $.each(result['data'], function(id, val){
            if(!select_student_ids.includes((val['id']|0))){
              var _option = '<option value="'+val['id']+'"';
              var _field = ['grade'];

              for(var i=0,n=_field.length;i<n;i++){
                _option += ' '+_field[i]+'="'+val[_field[i]]+'"';
              }
              _option+= '>'+val['name']+'</option>';
              student_id_form.append(_option);
              c++;
            }
          });
          if(c>0){
            var _width = student_id_form.attr("width");
            student_id_form.select2({
              width: _width,
              placeholder: '選択してください',
            });
            student_id_form.show();
            $("#select_student_none").hide();
          }
          else {
            student_id_form.hide();
            $("#select_student_none").show();
          }

        }
      },
      function(xhr, st, err) {
          alert("UI取得エラー(calendars.get_charge_students)");
      }
    );
  }
  </script>
  @endslot
@endcomponent
