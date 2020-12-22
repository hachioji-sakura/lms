@include('season_lesson.create_form')
<div id="">
    <div class="row p-2">
      @component('season_lesson.confirm_form', ['attributes' => $attributes, 'is_trial' => false, 'item'=> $item]) @endcomponent
      <div class="col-12  mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
      </div>
    </div>
</div>
