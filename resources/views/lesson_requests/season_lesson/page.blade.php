@include('lesson_requests.season_lesson.create_form')
<div id="">
    <div class="row p-2">
      @component($domain.'.season_lesson.confirm_form', ['attributes' => $attributes, 'is_trial' => false, 'item'=> $item]) @endcomponent
      @if($action=='delete')
      <div class="col-12  mb-1">
        <div id="delete_form">
          <form method="POST" action="/{{$domain}}/{{$item['id']}}">
            @csrf
            <input type="text" name="dummy" style="display:none;" / >
            @method('DELETE')
            <button type="button" class="btn btn-submit btn-danger btn-block" accesskey="delete_form" confirm="削除しますか？">
              申し込みをキャンセルする
            </button>
          </form>
        </div>
      </div>
      @endif
      <div class="col-12  mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
      </div>
    </div>
</div>
