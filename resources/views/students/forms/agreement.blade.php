<section id="member" class="content-header">
		<div class="card mb-2">
	    <div class="card-header d-flex p-0">
	    <h4 class="card-title p-3 text-sm">
				<i class="fa fa-file-signature"></i>
				基本契約内容
	    </h4>
	    </div>
	    <div class="card-body">
					<div class="row">
						{{--
						<div class="col-12 p-0">
		          <h5 class="p-2 mb-2 border-secondary bd-b">
								<i class="fa fa-file"></i>
								生徒様情報
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
						{{--
	          <div class="col-6 p-2 font-weight-bold" >生年月日</div>
	          <div class="col-6 p-2">{{$item->birth_day()}}</div>
						--}}
	          <div class="col-6 p-2 font-weight-bold" >学年</div>
	          <div class="col-6 p-2">{{$item->grade()}}</div>
	          <div class="col-6 p-2 font-weight-bold school_name_confirm" >学校名</div>
	          <div class="col-6 p-2">{{$item->school_name()}}</div>
						@for($i=1;$i<5;$i++)
						@if($item->user->has_tag('lesson',$i)==false) @continue @endif
						@if($item->user->get_enable_calendar_setting_count($i)==0) @continue @endif
		          <div class="col-6 p-2 font-weight-bold" >({{config('attribute.lesson')[$i]}})通塾回数/週</div>
		          <div class="col-6 p-2">週{{$item->user->get_enable_calendar_setting_count($i)}}回</div>
						@endfor						<div class="col-12 bd-b bd-gray"></div>
						<div class="col-6 p-2 font-weight-bold" >入会金</div>
	          <div class="col-6 p-2">
							{{number_format($item->get_entry_fee())}}円(税込み)
							<input type="hidden" name="agreements[entry_fee]" value="{{$item->get_entry_fee()}}">
	          </div>
						<div class="col-6 p-2 font-weight-bold" >月会費</div>
	          <div class="col-6 p-2">
							{{number_format($item->get_monthly_fee())}}円(税込み)
							<input type="hidden" name="agreements[monthly_fee]" value="{{$item->get_monthly_fee()}}">
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
						<?php
							$tuition_form = [];
							$is_exist = false;
						?>
						@foreach($item->user->get_enable_lesson_calendar_settings() as $lesson => $d0)
	          	@foreach($d0 as $schedule_method => $d1)
	            	@foreach($d1 as $lesson_week => $settings)
	              	@foreach($settings as $setting)
	              <?php
	              $setting = $setting->details();
	              ?>
								<div class="col-12 pl-2 bd-b bd-gray">
									<div class="row">
			              <div class="col-12 p-2 font-weight-bold" >
											{{$setting->lesson()}} / {{$setting->course()}}
										</div>
										<div class="col-12 p-2 pl-4" >
			                ・{{$setting->schedule_method()}}{{$setting["week_setting"]}}/{{$setting["timezone"]}}
			              </div>
			              <div class="col-12 p-2 pl-4" >
			                ・授業時間：{{$setting["course_minutes_name"]}}
			              </div>
			              <div class="col-12 p-2 pl-4" >
			                ・校舎：{{$setting["place_floor_name"]}}
			              </div>
			              <div class="col-12 p-2 pl-4">
			                ・科目：
			                @foreach($setting->subject() as $subject)
			                  {{$subject}}
			                @endforeach
			              </div>
			              <div class="col-12 p-2 pl-4" >
			                ・担当講師：{{$setting["teacher_name"]}}
			              </div>
		              <div class="col-12 p-2 pl-4" >
											<?php
											$is_exist = true;
											$setting_key = $setting->get_tag_value('lesson').'_';
											$setting_key .= $setting->get_tag_value('course_type').'_';
											$setting_key .= $setting->course_minutes.'_';
											$setting_key .= $setting->user_id.'_';
											if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true){
												$setting_key .= $setting->get_tag_value('subject');
											}
											else if($setting->get_tag_value('lesson')==4){
												$setting_key .= $setting->get_tag_value('kids_lesson');
											}
											?>
											<input type="hidden" name="agreement_statements[{{$setting_key}}][setting_key]" value="{{$setting_key}}">
											<input type="hidden" name="agreement_statements[{{$setting_key}}][teacher_id]" value="{{$setting->user->details('teachers')->id}}">
											<input type="hidden" name="agreement_statements[{{$setting_key}}][lesson_id]" value="{{$item->tags_value('lesson')}}">
											<input type="hidden" name="agreement_statements[{{$setting_key}}][course_type]" value="{{$setting->get_tag_value('course_type')}}">
											<input type="hidden" name="agreement_statements[{{$setting_key}}][course_minutes]" value="{{$item->tag_value('course_minutes')}}">
											<input type="hidden" name="agreement_statements[{{$setting_key}}][grade]" value="{{$item->tag_value('grade')}}">
											@foreach($setting->members as $member)
											 	@if($member->user_id == $item->user_id)
												<input type="hidden" name="agreement_statements[{{$setting_key}}][user_calendar_member_setting_id][]" value="{{$member->id}}">
												@endif
											@endforeach
											@for($i=1;$i<5;$i++)
											@if($item->user->has_tag('lesson',$i)==false) @continue @endif
											@if($item->user->get_enable_calendar_setting_count($i)==0) @continue @endif
							         	<input type="hidden" name="agreement_statements[{{$setting_key}}][lesson_week_count]" value="{{$item->user->get_enable_calendar_setting_count($i)}}">
											@endfor
											<input type="hidden" name="agreement_statements[{{$setting_key}}][is_exam]" value="0">
											@if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true)
											<input type="hidden" name="agreement_statements[{{$setting_key}}][subject]" value="{{$setting->get_tag_value('subject')}}">
											@endif

											@if(isset($input) && $input==true)
												@if(!isset($tuition_form[$setting_key]))
													<?php $tuition_form[$setting_key] = $setting->id; ?>
												@endif
												<div class="form-group">
													<label for="tuition" class="w-100">
														受講料
														@if($setting->id != $tuition_form[$setting_key])
															{{--
																カレンダー設定に対し、レッスン＋授業形態＋授業時間＋講師IDが同一の場合、
																同一受講料となる
															--}}
															<span class="right badge badge-warning ml-1">同一受講料設定あり</span>
														@else
														 <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
														@endif
													</label>

													@if($setting->id != $tuition_form[$setting_key])
														<span alt="{{$tuition_form[$setting_key]}}_tuition" class="ml-2 float-left mt-2">円 / 時間</span>
														<span class="ml-2 float-left mt-2">円 / 時間</span>
													@else
														<input type="text" id="{{$setting->id}}_tuition" name="agreement_statements[{{$setting_key}}][tuition]" class="form-control w-50 float-left tuition" required="true" maxlength=5 inputtype="numeric"
														 minvalue="1000"
														@if(isset($_edit) && $_edit==true)
 															value="{{$setting->get_tuition($item->user_id)}}" placeholder="(変更前) {{$setting->get_tuition($item->user_id)}}"
														@else
															value="{{$setting->get_tuition($item->user_id)}}" placeholder="(変更前) {{$setting->get_tuition($item->user_id)}}"
														@endif
														>
														<span class="ml-2 float-left mt-2">円 / 時間</span>
														{{-- TODO 修正lesson_week_count --}}
														<a href="javascript:void(0);" role="button" class="btn btn-sm btn-secondary float-left ml-2 tuition_calc"
															setting_id="{{$setting->id}}"
															lesson="{{$setting->get_tag_value('lesson')}}"
						                  course_type="{{$setting->get_tag_value('course_type')}}"
						                  course_minutes="{{$setting->course_minutes}}"
						                  teacher_id="{{$setting->user->details('teachers')->id}}"
															student_id="{{$item->id}}"
															grade="{{$item->tag_value('grade')}}"
															lesson_week_count="{{$item->user->get_enable_calendar_setting_count($setting->get_tag_value('lesson'))}}"
															@if($item->is_juken()==true)
												        is_juken="1"
												      @else
																is_juken="0"
												      @endif
															@if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true)
						                  subject="{{$setting->get_tag_value('subject')}}"
						                  @elseif($setting->get_tag_value('lesson')==4)
						                  subject="{{$setting->get_tag_value('kids_lesson')}}"
						                  @endif
														>
															<i class="fa fa-yen-sign"></i>
															自動設定
														</a>
													@endif
												</div>
											@else
											・受講料：
												@if(!empty($setting->get_tuition($item->user_id)))
													&yen;{{number_format($setting->get_tuition($item->user_id))}} / 時間
												@else
													<i class="fa fa-exclamation-triangle mr-1"></i>受講料設定がありません
												@endif
											@endif
										</div>
									</div>
								</div>
	              	@endforeach
	            	@endforeach
							@endforeach
						@endforeach
						@if($is_exist==false)
						<div class="alert">
						  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
						</div>

						@endif
	        </div>
	    </div>
		</div>
</section>
@component('tuitions.forms.calc_script', []) @endcomponent
<script>
$(function(){
	$("a.tuition_calc").each(function(index, element){
		set_init(element);
	});
	$("a.tuition_calc").click(function(index, element){
		set_init(this);
	});
	$("input.tuition").change(function(index, element){
		$('span[alt='+$(this).attr('name')+']').html($(this).val());
	});
	function set_init(element){
		var fields = ['lesson', 'course_type', 'subject', 'teacher_id', 'course_minutes', 'grade', 'is_juken', 'lesson_week_count', 'setting_id'];
	  var data = [];
		for(var i=0;i<fields.length;i++){
      data[fields[i]] = $(element).attr(fields[i]);
    }
	  var r = get_tuition(data["lesson"]|0, data["course_type"], data["grade"], data["is_juken"]|0, data["lesson_week_count"]|0, data["subject"], data["course_minutes"]|0, data["teacher_id"]);
		if($('input[name='+data['setting_id']+'_tuition]').val()==0 || util.isEmpty($('input[id='+data['setting_id']+'_tuition]').val()))$('input[id='+data['setting_id']+'_tuition]').val(r);
		$('span[alt='+data['setting_id']+'_tuition]').html(r);
	}
});
</script>
