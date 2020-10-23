<div id="admission_mail">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/admission">
    @component('trials.forms.admission_schedule', [ 'attributes' => $attributes, 'prefix'=>'', 'item' => $item, 'domain' => $domain, 'input' => true, 'agreement' => $item, 'active_tab' => 2]) @endcomponent
    @csrf
    <input type="text" name="dummy" style="display:none;" / >
    <section class="content-header">
    	<div class="container-fluid">
        {{--
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label for="remark" class="w-100">
                その他、連絡事項
                <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
              </label>
              <textarea type="text" id="body" name="remark" class="form-control" placeholder="例：受講料の訂正について、ご連絡いたします。" ></textarea>
            </div>
          </div>
        </div>
        --}}
    		<div class="row">
    			<div class="col-12 col-md-6 mb-1">
    				<button type="button" class="btn btn-submit btn-primary btn-block" accesskey="admission_mail" confirm="契約更新メールを送信しますか？"
            @if($item->agreement_statements->count() == 0)
              disabled
            @endif
            >
    					<i class="fa fa-envelope mr-1"></i>
    					契約更新メールを送信する
    				</button>
    			</div>
    			<div class="col-12 col-md-6 mb-1">
    				<a href="/{{$domain}}/{{$item->id}}" role="button" class="btn btn-secondary btn-block float-left mr-1">
    					<i class="fa fa-arrow-circle-left mr-1"></i>
    					{{__('labels.cancel_button')}}
    				</a>
    			</div>
    		</div>
    	</div>
    </section>
  </form>
</div>
