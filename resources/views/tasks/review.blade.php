<div id="review_form">
  <form method="POST" action="/tasks/{{$item->id}}/review">
    @csrf
    @method('PUT')
    <div class="row mb-2">
      <div class="col-12">
        <label for="evaluation" class="ml-2">
          {{__('labels.evaluation')}}
        </label>
        <span class="badge badge-danger ml-1">
          {{__('labels.required')}}
        </span>
        <div class="form-check">
          @for($i=1;$i<=5;$i++)
          <input class="frm-check-input icheck flat-green" type="radio" name="evaluation" id="evaluation" value="{{$i}}" required="true" checked>
          <label class="form-check-label" for="evaluation">
            {{$i}}
          </label>
          @endfor
        </div>
      </div>
    </div>
<!--
    <div class="row">
      <div class="col-12">
        <label for="review" class="ml-2">
          {{__('labels.review')}}
        </label>
        <span class="badge badge-primary ml-1">
          {{__('labels.optional')}}
        </span>
        <textarea name="review" class="form-control mb-2" placeholder="タスクをレビューしてください。"></textarea>
      </div>
    </div>
  -->
    <div class="row">
      <div class="col-6">
        <button type="submit" class="btn btn-submit btn-primary w-100">
          <i class="fa fa-pen"></i>
          {{__('labels.evaluation_button')}}
        </button>
      </div>
      <div class="col-6">
        <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
          <i class="fa fa-times-circle mr-1"></i>
          {{__('labels.back_button')}}
        </a>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){
  base.pageSettinged("review_form");
});
</script>
