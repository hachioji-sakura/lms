<div id="reapply">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/reapply">
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        <div class="row">
          <div class="col-12 mb-2 bg-warning p-4">
            <i class="fa fa-exclamation-triangle mr-2"></i>
          体験授業が決まらない場合、<br>
          希望日時を変更してもらうための連絡を送信します。
          </div>
        </div>
    		<div class="row">
    			<div class="col-12 col-md-6 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="reapply" >
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
