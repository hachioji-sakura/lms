<div class="row mt-2">
  <div class="col-12">
    <label>{{__('labels.curriculums_name')}}</label>
    <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    <select name="curriculum_ids[]" class="form-control select2"  width=100%  multiple="multiple">
      @foreach($curriculums as $curriculum)
      <option value="{{$curriculum->id}}"
      @if(!empty($item) && $_edit)
        {{$item->curriculums->contains($curriculum->id)  ? "selected" : "" }}
      @endif
      >{{$curriculum->name}}</option>
      @endforeach
    </select>
    <a class="btn btn-primary" role="button" id="add_curriculum">
      <i class="fa fa-plus"></i>
    </a>
    <div id="curriculums">

    </div>
  </div>
</div>

<script>
$("#add_curriculum").on("click", function(e){
  $("#curriculums").load('{{request()->task_type}}');
});
$("#select_subject").on('change', function(e){
  $('#curriculums').load( "{{url('/curriculums')}}?search_subject_id="+$('select#select_subject').val() );
});
</script>
