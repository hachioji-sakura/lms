<div id="create_review">
  <form method="POST" action="/task_reviews">
    @csrf
    <input type="hidden" name="task_id" value="{{$task_id}}"
    <div class="row mb-2">
      <div class="col-12">
        <label for="evaluation" class="ml-2">
          {{__('labels.task_understanding')}}
        </label>
        <span class="badge badge-danger ml-1">
          {{__('labels.required')}}
        </span>
        <div class="form-check">
          @for($i=4;$i>=1;$i--)
          <input class="frm-check-input icheck flat-green ml-2" type="radio" name="evaluation" id="evaluation{{$i}}" value="{{$i}}" required="true" {{$i == 4 ? 'checked' : ''}}>
          <label class="form-check-label" for="evaluation{{$i}}">
            {{config('attribute.task_review_evaluation')[$i]}}
          </label>
          <br />
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
    <div class="row mt-2">
      <div class="col-6">
        <button type="submit" class="btn btn-submit btn-primary w-100" accesskey="create_review">
          <i class="fa fa-pen"></i>
          {{__('labels.review_button')}}
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
