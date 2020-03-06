<div id="ask_hope_to_join">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/ask_hope_to_join">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        <div class="row">
          <div class="col-12 mb-2 bg-warning p-4">
            <i class="fa fa-exclamation-triangle mr-2"></i>
          入会希望を受け取るための連絡を送信します。<br>
          その後、入会希望の返信があった場合、通常授業の設定と入会案内連絡を送信してください
          </div>
        </div>
    		<div class="row">
    			<div class="col-12 col-md-6 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="ask_hope_to_join" >
    					<i class="fa fa-envelope mr-1"></i>
    					{{__('labels.send_button')}}
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
