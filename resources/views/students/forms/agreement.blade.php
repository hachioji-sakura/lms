<section id="member" class="content-header">
	<div class="container-fluid">
		<div class="card">
	    <div class="card-header d-flex p-0">
	    <h5 class="card-title p-3 text-sm">
				{{$item->name()}}　様
	    </h5>
	    <ul class="nav nav-pills ml-auto p-2">
	        <li class="tab-link text-secondary mr-2"><a class="btn btn-sm btn-default
						@if(!isset($active_tab) || $active_tab==1)
							active
						@endif
						" href="#tab_{{$item->id}}_1" data-toggle="tab">
						<i class="fa fa-file"></i>
						<span class="btn-label">生徒様情報</span>
					</a></li>
	        <li class="tab-link text-secondary"><a class="btn btn-sm btn-default
						@if(isset($active_tab) && $active_tab==2)
							active
						@endif
						" href="#tab_{{$item->id}}_2" data-toggle="tab">
						<i class="fa fa-clock"></i>
						<span class="btn-label">通塾スケジュール</span>
					</a></li>
	    </ul>
	    </div>
	    <div class="card-body">
		    <div class="tab-content">
		        <div class="tab-pane
						@if(!isset($active_tab) || $active_tab==1)
							active
						@endif
						" id="tab_{{$item->id}}_1">
							<div class="row bd-r bd-l bd-b">
								<div class="col-12 p-0">
				          <h5 class="bg-gray color-palette p-2 mb-2">
										<i class="fa fa-file"></i>
										生徒様情報
									</h5>
								</div>
			          <div class="col-6 p-2 font-weight-bold" >氏名</div>
			          <div class="col-6 p-2">
									<ruby style="ruby-overhang: none">
										<rb>{{$item->name()}}</rb>
										<rt>{{$item->kana()}}</rt>
									</ruby>
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
			          <div class="col-6 p-2 font-weight-bold" >レッスン</div>
			          <div class="col-6 p-2">{{$item->tags_name('lesson')}}</div>
			          <div class="col-6 p-2 font-weight-bold" >通塾回数/週</div>
			          <div class="col-6 p-2">週{{$item->tag_value('lesson_week_count')}}回</div>
			          <div class="col-6 p-2 font-weight-bold" >授業時間</div>
			          <div class="col-6 p-2">{{$item->tag_name('course_minutes')}}</div>
			          @if($item->user->has_tag('lesson',2)==true)
			          <div class="col-6 p-2 font-weight-bold" >英会話コース</div>
			          <div class="col-6 p-2">{{$item->tag_name('english_talk_course_type')}}</div>
			          @endif
			          @if($item->user->has_tag('lesson',4)==true)
			          <div class="col-6 p-2 font-weight-bold" >習い事コース</div>
			          <div class="col-6 p-2">{{$item->tag_name('kids_lesson_course_type')}}</div>
			          @endif
			        </div>
		        </div>
		        <div class="tab-pane
						@if(isset($active_tab) && $active_tab==2)
							active
						@endif
						" id="tab_{{$item->id}}_2">
							<div class="row bd-r bd-l bd-b">
								<div class="col-12 p-0">
				          <h5 class="bg-gray color-palette p-2 mb-2">
										<i class="fa fa-clock"></i>
										通塾スケジュール
									</h5>
								</div>
								<?php
									$tuition_form = [];
								?>
			          @foreach($item->user->calendar_setting() as $schedule_method => $d1)
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
													$setting_key = $setting->get_tag_value('lesson').'_';
													$setting_key .= $setting->get_tag_value('course_type').'_';
													$setting_key .= $setting->get_tag_value('course_minutes').'_';
													$setting_key .= $setting->user_id.'_';
													if($setting->get_tag_value('lesson')==2 && $setting->has_tag('english_talk_lesson', 'chinese')==true){
														$setting_key .= $setting->get_tag_value('subject');
													}
													else if($setting->get_tag_value('lesson')==4){
														$setting_key .= $setting->get_tag_value('kids_lesson');
													}
													?>
													@if(isset($input) && $input==true)
														@if(!isset($tuition_form[$setting_key]))
															<?php $tuition_form[$setting_key] = $setting->id; ?>
														@endif
														<div class="form-group">
															<label for="tuition" class="w-100">
																受講料
																@if($setting->id != $tuition_form[$setting_key])
																	<span class="right badge badge-warning ml-1">同一受講料設定あり</span>
																@else
																 <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
																@endif
															</label>

															@if($setting->id != $tuition_form[$setting_key])
																<span alt="{{$tuition_form[$setting_key]}}_tuition" class="ml-2 float-left mt-2">円 / 時間</span>
																<span class="ml-2 float-left mt-2">円 / 時間</span>
															@else
																<input type="text" id="{{$setting->id}}_tuition" name="{{$tuition_form[$setting_key]}}_tuition" class="form-control w-50 float-left tuition" required="true" maxlength=5 inputtype="numeric"
																 minvalue="1000"
																@if(isset($_edit) && $_edit==true)
																 value="{{$item['tuition']}}" placeholder="(変更前) {{$item['tuition']}}"
																@endif
																>
																<span class="ml-2 float-left mt-2">円 / 時間</span>
																<a href="javascript:void(0);" role="button" class="btn btn-sm btn-secondary float-left ml-2 tuition_calc"
																	setting_id="{{$setting->id}}"
																	lesson="{{$setting->get_tag_value('lesson')}}"
								                  course_type="{{$setting->get_tag_value('course_type')}}"
								                  course_minutes="{{$setting->get_tag_value('course_minutes')}}"
								                  teacher_id="{{$setting->user->details()->id}}"
																	student_id="{{$item->id}}"
																	grade="{{$item->tag_value('grade')}}"
																	lesson_week_count="{{$item->tag_value('lesson_week_count')}}"
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
																	初期値
																</a>
															@endif
														</div>
													@else
													・受講料：
														@if(!empty($item->get_tuition($setting, false)))
															&yen;{{$item->get_tuition($setting, false)}} / 時間
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
						</div>
	        </div>
		    </div>
	    </div>
		</div>
	</div>
	@component('tuitions.forms.calc_script', []) @endcomponent
</section>
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
	  data["grade"] = data["grade"].substring(0,1);
	  var r = get_tuition(data["lesson"]|0, data["course_type"], data["grade"], data["is_juken"]|0, data["lesson_week_count"]|0, data["subject"], data["course_minutes"]|0);
		$('input[name='+data['setting_id']+'_tuition]').val(r);
		$('span[alt='+data['setting_id']+'_tuition]').html(r);
	}
});
</script>
