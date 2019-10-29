<div class="card mt-2">
    <div class="card-header d-flex p-0">
    <h5 class="card-title p-3 text-sm">
			<i class="fa fa-id-card mr-1"></i>
			ご契約者様情報
    </h5>
    </div>
    <div class="card-body">
			<div class="row">
				{{--
				<div class="col-12 p-0">
          <h5 class="p-2 mb-2 border-secondary bd-b">
						<i class="fa fa-file"></i>
						ご契約者様情報
					</h5>
				</div>
				--}}
        <div class="col-6 p-2 font-weight-bold" >氏名</div>
        <div class="col-6 p-2">
					<ruby style="ruby-overhang: none">
						<rb>{{$item->name()}}</rb>
						<rt>{{$item->kana()}}</rt>
					</ruby>
					<span class="ml-2">様</span>
					<span class="text-xs mx-2">
						<small class="badge badge-{{config('status_style')[$item->status]}} mt-1 mr-1">
							{{$item->status_name()}}
						</small>
					</span>
        </div>
        <div class="col-6 p-2 font-weight-bold" >メールアドレス</div>
        <div class="col-6 p-2">{{$item["email"]}}</div>
        <div class="col-6 p-2 font-weight-bold" >連絡先</div>
        <div class="col-6 p-2">{{$item["phone_no"]}}</div>
				<div class="col-6 p-2 font-weight-bold" >住所</div>
        <div class="col-6 p-2">{{$item["address"]}}</div>
      </div>
    </div>
  </div>
@component('tuitions.forms.calc_script', []) @endcomponent
