<div id="admission_mail">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/ask_candidate">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        <div class="row mb-4">
          <div class="col-12">
            {!!nl2br(__('messages.message_trial_candidate_date1'))!!}
          </div>
          <div class="col-12 my-1">
              <textarea type="text" id="body" name="add_message" class="form-control" placeholder="（追加文面）" ></textarea>
          </div>
          <div class="col-12">
            {!!nl2br(__('messages.message_trial_candidate_date2'))!!}
            <br>
            <a target="__blank" href="{{config('app.url')}}/trials/{{$item->id}}/add_candidate_date" >
              {{config('app.url')}}/trials/{{$item->id}}/add_candidate_date?key=xxxxxxxxxxxxxxxx
            </a>
          </div>
        </div>
    		<div class="row">
    			<div class="col-12 col-md-6 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="admission_mail" confirm="体験授業の候補日を訊く連絡メールを送信しますか？">
    					<i class="fa fa-envelope mr-1"></i>
    					体験授業の候補日を訊く連絡メールを送信する
    				</button>
    			</div>
          <div class="col-12 col-md-6 mb-1">
              <button type="reset" class="btn btn-secondary btn-block">
                  {{__('labels.close_button')}}
              </button>
          </div>
    		</div>
    	</div>
    </section>
  </form>
</div>
