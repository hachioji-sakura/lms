<section id="member" class="content-header">
	<div class="card mb-2">
    <div class="card-header d-flex p-0">
    <h4 class="card-title p-3 text-sm">
			<i class="fa fa-file-signature"></i>
			基本契約内容
    </h4>
    </div>
    <div class="card-body">
			@if(!empty($agreement))
				<input type="hidden" name="agreements[id]" value={{$agreement->id}}>
				<div class="row">
          <div class="col-6 p-2 font-weight-bold" >氏名</div>
          <div class="col-6 p-2">
						<ruby style="ruby-overhang: none">
							<rb>{{$item->name()}}</rb>
							<rt>{{$item->kana()}}</rt>
						</ruby>
						<span class="ml-2">様</span>
						@if($domain!='asks')
						<span class="text-xs mx-2">
							<small class="badge badge-{{config('status_style')[$item->status]}} mt-1 mr-1">
								{{$item->status_name()}}
							</small>
						</span>
						@endif
          </div>
          <div class="col-6 p-2 font-weight-bold" >性別</div>
          <div class="col-6 p-2">{{$item->gender()}}</div>
          <div class="col-6 p-2 font-weight-bold" >学年</div>
          <div class="col-6 p-2">{{$item->grade()}}</div>
          <div class="col-6 p-2 font-weight-bold school_name_confirm" >学校名</div>
          <div class="col-6 p-2">{{$item->school_name()}}</div>
					@for($i=1;$i<5;$i++)
					@if($item->user->has_tag('lesson',$i)==false) @continue @endif
					@if($item->user->get_enable_calendar_setting_count($i)==0) @continue @endif
	          <div class="col-6 p-2 font-weight-bold" >({{config('attribute.lesson')[$i]}})通塾回数/週</div>
	          <div class="col-6 p-2">週{{$agreement->agreement_statements->where('lesson_id',$i)->first()->lesson_week_count}}回</div>
					@endfor
					<div class="col-12 bd-b bd-gray"></div>
					<div class="col-6 p-2 font-weight-bold" >入会金</div>
          <div class="col-6 p-2">
						{{number_format($agreement->entry_fee)}}円(税込み)
          </div>
					<div class="col-6 p-2 font-weight-bold" >月会費</div>
          <div class="col-6 p-2">
						{{number_format($agreement->monthly_fee)}}円(税込み)
          </div>
        </div>
			</div>
		</div>

		<div class="card mb-2">
	    <div class="card-header d-flex p-0">
	    <h4 class="card-title p-3 text-sm">
				<i class="fa fa-user-clock"></i>
				通塾内容
	    </h4>
	    </div>
	    <div class="card-body">
				<div class="row">
					{{--
					<div class="col-12 p-0">
						<h5 class="p-2 mb-2 border-secondary bd-b">
							<i class="fa fa-clock"></i>
							通塾スケジュール
						</h5>
					</div>
					--}}
					@foreach($agreement->agreement_statements as $statement)
						<div class="col-12 pl-2 bd-b bd-gray">
							<div class="row">
								<div class="col-12 p-2 font-weight-bold" >
									{{$statement->lesson_name}} / {{$statement->course_type_name}}
								</div>

								@foreach($statement->user_calendar_member_settings as $member)
									<div class="col-12 p-2 pl-4" >
										・{{$member->setting->schedule_method()}}{{$member->setting->details()["week_setting"]}}/{{$member->setting->details()["timezone"]}}
										<ul>
											<li>校舎：{{$member->setting->details()["place_floor_name"]}}</li>
											<li>科目：
											@foreach($member->setting->subject() as $subject)
											{{$subject}}
											@endforeach</li>
										</ul>
									</div>
								@endforeach
								<div class="col-12 p-2 pl-4" >
									・授業時間：{{$statement->course_minutes_name}}
								</div>
								{{--
								<div class="col-12 p-2 pl-4">
									・科目：
									@foreach($setting->subject() as $subject)
										{{$subject}}
									@endforeach
								</div>
								--}}
								<div class="col-12 p-2 pl-4" >
									・担当講師：{{$statement->teacher_name}}
								</div>
							<div class="col-12 p-2 pl-4" >
									@if(isset($input) && $input==true)
										<div class="form-group">
											<label for="tuition" class="w-100">
												受講料
												 <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
											</label>
											<input type="text" id="{{$statement->id}}_tuition" name="agreement_statements[{{$statement->id}}][tuition]" class="form-control w-50 float-left tuition" required="true" maxlength=5 inputtype="numeric"
											 minvalue="1000" value="{{$statement->tuition==0 ? '': $statement->tuition}}" placeholder="(受講料定義)  {{$statement->tuition==0 ?'見つかりませんでした。' : $statement->tition}}"
											>
											<span class="ml-2 float-left mt-2">円 / 時間</span>
										</div>
									@else
									・受講料：
										@if(!empty($statement->tuition))
											&yen;{{number_format($statement->tuition)}} / 時間
										@else
											<i class="fa fa-exclamation-triangle mr-1"></i>受講料設定がありません
										@endif
									@endif
								</div>
							</div>
						</div>
					@endforeach
					@else
					<div class="alert">
					  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
					</div>
					@endif
        </div>
	    </div>
		</div>
</section>
