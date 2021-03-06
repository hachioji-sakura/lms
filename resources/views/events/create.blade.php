<div id="{{$domain}}_create">
  @if(isset($_edit) && $_edit==true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
    @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
  @csrf
  <input type="text" name="dummy" style="display:none;" / >
　  <div class="row">
      <div class="col-12">
        <label for="event_template_id" class="w-100">
          テンプレート
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <div class="">
          <select name="event_template_id" class="form-control select2" width=100% onChange="template_change()">
            <option value="">({{__('labels.select')}})</option>
            @foreach($templates as $template)
            <option value="{{$template->id}}"
              @if(isset($_edit) && $_edit==true && $item->event_template_id == $template->id)
              selected
              @endif
            >{{$template->title}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-12">
        <small class="badge badge-primary mt-1 mr-1" id="role"></small>
        <small class="badge badge-primary mt-1 mr-1" id="lesson"></small>
        <small class="badge badge-primary mt-1 mr-1" id="grade"></small>
      </div>
      <script>
      function template_change(){
        var template_id = $('select[name="event_template_id"]').val();
        var url  = 'event_templates/'+template_id+'?api=1';
        service.getAjax(false, url, null,
          function(result, st, xhr) {
            var t = $('input[name="title"]');
            var b = $('textarea[name="body"]');
            var data =  result["data"];
            $('#role').html('送信対象：'+data['role']);
            $('#lesson').html('条件（部門）：'+data['lesson']);
            $('#grade').html('条件（学年）：'+data['grade']);
            console.log(data);
            if(confirm('件名と内容を、テンプレートの設定値にしますか？')){
              t.val(data['title']);
              b.val(data['body']);
            }
          },
          function(xhr, st, err) {
            var messageCode = "error";
            var messageParam= "\n"+err.message+"\n"+xhr.responseText;
            alert("UIエラー\n"+messageParam);
          }
        ,true);
      }
      </script>
      <div class="col-12">
        <div class="form-group">
          <label for="title" class="w-100">
            件名
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <input type="text" id="title" name="title" class="form-control" required="true" maxlength=50
          @if(isset($_edit) && $_edit==true)
           value="{{$item['title']}}" placeholder="(変更前) {{$item['title']}}">
          @else
           placeholder="(例)2020年冬期講習のお知らせ">
          @endif
        </div>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="event_from_date" class="w-100">
            開催期間（開始）
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" name="event_from_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['event_from_date']) && $item['event_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_from_date']))}}"
            @elseif(isset($item) && isset($item['event_from_date']) && $item['event_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_from_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="event_to_date" class="w-100">
            開催期間（終了）
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="event_to_date" name="event_to_date" class="form-control float-left" required="true" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['event_to_date']) && $item['event_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_to_date']))}}"
            @elseif(isset($item) && isset($item['event_to_date']) && $item['event_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['event_to_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="response_from_date" class="w-100">
            回答期間(開始)
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="response_from_date" name="response_from_date" class="form-control float-left" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['response_from_date']) && $item['response_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_from_date']))}}"
            @elseif(isset($item) && isset($item['response_from_date']) && $item['response_from_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_from_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 mb-1">
        <div class="form-group">
          <label for="response_to_date" class="w-100">
            回答期間(終了)
            <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
            <input type="text" id="response_to_date" name="response_to_date" class="form-control float-left" uitype="datepicker" placeholder="例：{{date('Y/m/d')}}"
            @if(isset($_edit) && $_edit==true && isset($item) && isset($item['response_to_date']) && $item['response_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_to_date']))}}"
            @elseif(isset($item) && isset($item['response_to_date']) && $item['response_to_date']!='9999-12-31')
              value="{{date('Y/m/d', strtotime($item['response_to_date']))}}"
            @else
              value = "{{date('Y/m/d')}}"
            @endif
            @if(!(isset($_edit) && $_edit==true))
            minvalue="{{date('Y/m/d')}}"
            @endif
            >
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="body" class="w-100">
            内容
            <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
          </label>
          <textarea type="text" name="body" class="form-control"  maxlength=5000
          @if(isset($_edit) && $_edit==true)
            placeholder="(変更前) {{$item->body}}" >{{$item->body}}</textarea>
          @else
            placeholder="5000文字まで" ></textarea>
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 mb-1">
          <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="{{$domain}}_create">
            @if(isset($_edit) && $_edit==true)
              {{__('labels.update_button')}}
            @else
              {{__('labels.add_button')}}
            @endif
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-md-6 mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
            {{__('labels.cancel_button')}}
          </button>
      </div>
    </div>
  </form>
</div>
