    <label>{{__('labels.curriculums_name')}}</label>
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    {{--新規カリキュラム追加は別画面でやる。要望に備えてコメントアウト
    <a href="javascript:void(0);" id="add_curriculum" class="btn btn-default btn-sm ml-1" title="{{__('labels.curriculums').__('labels.add_button')}}">
      <i class="fa fa-plus"></i>
    </a>
    <a href="javascript:void(0);" id="clear_curriculum" class="btn btn-default btn-sm ml-1" title="{{__('labels.curriculums').__('labels.delete_button')}}">
      <i class="fa fa-trash"></i>
    </a>
    --}}
    <select name="curriculum_ids[]" class="form-control select2" id="select_curriculum" width=100%  multiple="multiple">
      @foreach($curriculums as $curriculum)
      <option value="{{$curriculum->id}}"
      @if(!empty($item) && $_edit)
        {{$item->curriculums->contains($curriculum->id)  ? "selected" : "" }}
      @endif
      >{{$curriculum->name}}</option>
      @endforeach
    </select>
    <script>
      $("#add_curriculum").on('click', function(e){
        $("#new_curriculums").append('<input type="text" class="form-control mt-1" name="new_curriculums[]" placeholder="{{__('labels.curriculums').__('labels.add')}}"  required="true">');
      });
      $("#clear_curriculum").on('click', function(e){
        console.log('hoge');
        $('#new_curriculums').empty();
      });
    </script>
