<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Auth::routes();
Route::group(['middleware' => 'request.trace', 'prefix' => ''], function() {
  //indexページをログインにする
  Route::redirect('/', '/login', 301);
  Route::get('send_access_key','AuthController@send_access_key');

  Route::get('managers/login','ManagerController@login');
  Route::get('auth','AuthController@auth');
  Route::get('forget','AuthController@forget');
  Route::post('forget','AuthController@reset_mail');
  Route::get('users/email/{email}','UserController@email_check');


  Route::get('password','UserController@password');
  Route::post('password','UserController@password_update');
  Route::get('password/setting','AuthController@password_setting');
  Route::post('password/setting','AuthController@password_settinged');


  Route::post('credentials/{id?}','AuthController@credential');


  Route::get('auth/mail','AuthController@mail_send');
  //Auth::routesのログアウトは、postのためgetのルーティングを追加
  Route::get('logout','Auth\LoginController@logout');

  Route::get('places/phone_list','PlaceController@phone_list');
  Route::resource('places','PlaceController');
  Route::resource('place_floors','PlaceFloorController');

  Route::get('auth/mail','AuthController@mail_send');

  Route::get('events/{id}/to_inform','EventController@to_inform_page');
  Route::post('events/{id}/to_inform','EventController@to_inform');
  Route::resource('events','EventController');
  Route::resource('event_users','EventUserController');
  Route::resource('event_templates','EventTemplateController');

  Route::post('upload_images','ImageController@upload_images');
  Route::resource('images','ImageController');
  Route::get('import/{object?}','ImportController@index');
  Route::post('import/{object?}','ImportController@import');

  Route::get('api_attributes/{select_key?}','GeneralAttributeController@api_index');
  Route::resource('attributes','GeneralAttributeController');
  Route::resource('milestones','MilestoneController');
  Route::get('text_materials/{id}/shared','TextMaterialController@shared_page');
  Route::put('text_materials/{id}/shared','TextMaterialController@shared');
  Route::get('text_materials/bulk_shared','TextMaterialController@bulk_shared_page');
  Route::put('text_materials/bulk_shared','TextMaterialController@bulk_shared');

  Route::resource('text_materials','TextMaterialController');

  Route::resource('school_grades','SchoolGradeController');
  Route::resource('school_grade_reports','SchoolGradeReportController');

  Route::resource('exams','ExamController');
  Route::post('exams/create','ExamController@store');  Route::resource('exam_results','ExamResultController');


  Route::get('comments/{id}/publiced','CommentController@publiced_page');
  Route::put('comments/{id}/publiced','CommentController@publiced');
  Route::put('comments/{id}/checked','CommentController@checked');
  Route::put('comments/{id}/importanced','CommentController@importanced');
  Route::resource('comments','CommentController');

  Route::get('announcements/{id}/publiced','AnnouncementController@publiced_page');
  Route::put('announcements/{id}/publiced','AnnouncementController@publiced');
  Route::put('announcements/{id}/checked','AnnouncementController@checked');
  Route::resource('announcements','AnnouncementController');

  Route::put('calendar_settings/{id}/remind','UserCalendarSettingController@remind');
  Route::get('calendar_settings/{id}/status_update/{status}','UserCalendarSettingController@status_update_page');
  Route::put('calendar_settings/{id}/status_update/{status}','UserCalendarSettingController@status_update');
  Route::get('calendar_settings/all_to_calendar','UserCalendarSettingController@all_to_calendar_page');
  Route::get('calendar_settings/to_calendar_data','UserCalendarSettingController@to_calendar_data');
  Route::post('calendar_settings/to_calendar','UserCalendarSettingController@to_calendar');
  Route::resource('calendar_settings','UserCalendarSettingController');
  Route::get('calendar_settings/{id}/to_calendar','UserCalendarSettingController@to_calendar_page');
  Route::get('calendar_settings/{id}/delete_calendar','UserCalendarSettingController@delete_calendar_page');
  Route::post('calendar_settings/{id?}/to_calendar','UserCalendarSettingController@to_calendar');
  Route::post('calendar_settings/{id}/delete_calendar','UserCalendarSettingController@delete_calendar');
  Route::get('calendar_settings/{id}/to_calendar_data','UserCalendarSettingController@to_calendar_data');
  Route::get('calendar_settings/check','UserCalendarSettingController@setting_check');
  Route::get('calendar_settings/{id}/fee','UserCalendarSettingController@get_fee');

  Route::resource('calendar_members','UserCalendarMemberController');

  Route::get('calendars/{id}/status_update/{status}','UserCalendarController@status_update_page');
  Route::put('calendars/{id}/status_update/{status}','UserCalendarController@status_update');
  Route::put('calendars/{id}/remind','UserCalendarController@remind');
  Route::put('calendars/{id}/cancel','UserCalendarController@force_cancel');
  Route::get('calendars/{id}/rest_change','UserCalendarController@rest_change_page');
  Route::put('calendars/{id}/rest_change','UserCalendarController@rest_change');
  Route::get('calendars/{id}/members/create','UserCalendarController@member_create_page');
  Route::post('calendars/{id}/members','UserCalendarController@member_create');
  Route::get('calendars/{id}/members/setting','UserCalendarController@member_setting_page');
  Route::put('calendars/{id}/members/setting','UserCalendarController@member_setting');
  Route::get('calendars/{id}/asks/teacher_change', 'UserCalendarController@teacher_change_page');
  Route::put('calendars/{id}/teacher_change', 'UserCalendarController@teacher_change');

  Route::get('calendars/check','UserCalendarController@setting_check');


  /*
  Route::get('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_page_no_login');
  Route::put('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_no_login');
  */
  Route::get('api_calendars/{user_id?}/{from_date?}/{to_date?}','UserCalendarController@api_index');
  Route::get('api_english_group_calendars','UserCalendarController@api_english_group');
  Route::resource('calendars','UserCalendarController');


  Route::get('trials/{id}/dialog','TrialController@show_dialog');
  Route::get('trials/{id}/to_calendar','TrialController@to_calendar');
  Route::post('trials/{id}/to_calendar','TrialController@to_calendar_confirm');
  Route::get('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting');
  Route::post('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting_update');
  Route::get('trials/{id}/admission','TrialController@admission_mail');
  Route::post('trials/{id}/admission','TrialController@admission_mail_send');

  Route::get('trials/{id}/cancel','TrialController@show_cancel_page');
  Route::put('trials/{id}/cancel','TrialController@cancel');

  Route::get('trials/{id}/cancel','TrialController@show_cancel_page');
  Route::put('trials/{id}/cancel','TrialController@cancel');

  Route::get('trials/{id}/ask_hope_to_join','TrialController@ask_hope_to_join');
  Route::post('trials/{id}/ask_hope_to_join','TrialController@ask_hope_to_join_mail_send');


  Route::get('trials/{id}/ask_candidate','TrialController@ask_candidate');
  Route::post('trials/{id}/ask_candidate','TrialController@ask_candidate_mail_send');
  Route::get('trials/{id}/candidate_date','TrialController@candidate_date_edit');
  Route::put('trials/{id}/candidate_date','TrialController@candidate_date_update');


  /*
  Route::get('trials/{id}/{status}','TrialController@status_update_page');
  Route::put('trials/{id}/{status}','TrialController@status_update');
  */
  Route::resource('trials','TrialController');
  Route::get('entry','TrialController@trial');
  Route::post('entry','TrialController@trial_store');

  Route::get('parents/{id}/trial_request','StudentParentController@trial_request_page');
  Route::post('parents/{id}/trial_request','StudentParentController@trial_request');


  Route::get('api_lectures','LectureController@api_index');
  Route::get('api_course','LectureController@api_index');
  Route::resource('lectures','LectureController');


  Route::resource('publisher','PublisherController');
  Route::resource('textbooks','TextbookController');

  Route::get('teachers/{id}/to_manager','TeacherController@to_manager_page');
  Route::post('teachers/{id}/to_manager','TeacherController@to_manager');
  Route::get('teachers/{id}/students','TeacherController@get_charge_students');
/* TODO:charge_studentsの追加が必要の場合
  Route::get('teachers/{id}/students/create','TeacherController@add_charge_student_page');
  Route::post('teachers/{id}/students','TeacherController@add_charge_student');
*/
  Route::get('register','StudentParentController@register');
  Route::post('register','StudentParentController@register_update');
  Route::get('teachers/entry','TeacherController@entry');
  Route::post('teachers/entry','TeacherController@entry_store');
  Route::get('teachers/register','TeacherController@register');
  Route::post('teachers/register','TeacherController@register_update');
  Route::get('teachers/{id}/month_work/{target_moth?}','TeacherController@month_work');
  Route::post('teachers/{id}/month_work','TeacherController@month_work_confirm');

  Route::get('managers/entry','ManagerController@entry');
  Route::post('managers/entry','ManagerController@entry_store');
  Route::get('managers/register','ManagerController@register');
  Route::post('managers/register','ManagerController@register_update');
  Route::get('managers/{id}/month_work/{target_moth?}','ManagerController@month_work');
  Route::post('managers/{id}/month_work','ManagerController@month_work_confirm');

  Route::get('signup','StudentParentController@entry');
  Route::post('signup','StudentParentController@entry_store');
  Route::get('parents/register','StudentParentController@register');
  Route::post('parents/register','StudentParentController@register_update');


  Route::get('students/{id}/setting','StudentController@setting_page');
  Route::get('teachers/{id}/setting','TeacherController@setting_page');


  Route::get('students/{id}/email_edit','StudentController@email_edit_page');
  Route::get('managers/{id}/email_edit','ManagerController@email_edit_page');
  Route::get('teachers/{id}/email_edit','TeacherController@email_edit_page');
  Route::get('parents/{id}/email_edit','StudentParentController@email_edit_page');
  Route::put('students/{id}/email_edit','StudentController@email_edit');
  Route::put('managers/{id}/email_edit','ManagerController@email_edit');
  Route::put('teachers/{id}/email_edit','TeacherController@email_edit');
  Route::put('parents/{id}/email_edit','StudentParentController@email_edit');

  Route::get('students/{id}/agreement','StudentController@agreement_page');
  Route::get('managers/{id}/retirement','ManagerController@retirement_page');
  Route::get('parents/{id}/agreement','StudentParentController@agreement_page');
  Route::get('students/{id}/retirement','StudentController@retirement_page');
  Route::get('teachers/{id}/retirement','TeacherController@retirement_page');
  Route::get('parents/{id}/retirement','StudentParentController@retirement_page');
  Route::put('students/{id}/retirement','StudentController@retirement');
  Route::put('managers/{id}/retirement','ManagerController@retirement');
  Route::put('teachers/{id}/retirement','TeacherController@retirement');
  Route::put('parents/{id}/retirement','StudentParentController@retirement');
  Route::get('students/{id}/regular','StudentController@regular_page');
  Route::put('students/{id}/regular','StudentController@regular_update');
  Route::get('teachers/{id}/regular','TeacherController@regular_page');
  Route::put('teachers/{id}/regular','TeacherController@regular_update');
  Route::get('managers/{id}/regular','ManagerController@regular_page');
  Route::put('managers/{id}/regular','ManagerController@regular_update');
  Route::get('parentss/{id}/regular','StudentParentController@regular_page');
  Route::put('parentss/{id}/regular','StudentParentController@regular_update');
  Route::get('parents/{id}/remind','StudentParentController@remind_page');
  Route::get('students/{id}/remind','StudentController@remind_page');
  Route::get('managers/{id}/remind','ManagerController@remind_page');
  Route::get('teachers/{id}/remind','TeacherController@remind_page');
  Route::post('parents/{id}/remind','StudentParentController@remind');
  Route::post('students/{id}/remind','StudentController@remind');
  Route::post('managers/{id}/remind','ManagerController@remind');
  Route::post('teachers/{id}/remind','TeacherController@remind');
  Route::get('students/{id}/tag','StudentController@tag_page');
  Route::post('students/{id}/tag','StudentController@update');
  Route::get('teachers/{id}/tag','TeacherController@tag_page');
  Route::post('teachers/{id}/tag','TeacherController@update');
  Route::get('managers/{id}/tag','ManagerController@tag_page');
  Route::post('managers/{id}/tag','ManagerController@update');

  Route::get( 'students/{id}/create_login_info' , 'StudentController@create_login_info_page');
  Route::put( 'students/{id}/create_login_info' , 'StudentController@set_login_info');

  Route::get('students/{id}/subject','StudentController@get_subject');
  Route::get('teachers/{id}/subject','TeacherController@get_subject');

  Route::resource('parents','StudentParentController');
  Route::resource('students','StudentController');
  Route::resource('managers','ManagerController');
  Route::resource('teachers','TeacherController');
  Route::resource('comments','CommentController');
  Route::resource('user_examinations','UserExaminationController');
  Route::get('icon/change','ImageController@icon_change_page');
  Route::put('icon/change','ImageController@icon_change');


  //Route::post('students/{id}/comments/create','CommentController@student_comments_store');
  Route::get('students/{id}/calendar','StudentController@calendar');
  Route::get('students/{id}/schedule','StudentController@schedule');
  Route::get('students/{id}/unsubscribe','StudentController@unsubscribe');
  Route::get('students/{id}/recess','StudentController@recess');
  Route::get('students/{id}/late_arrival','StudentController@late_arrival');
  Route::get('students/{id}/resume','StudentController@resume');
  Route::get('students/{id}/tuition','StudentController@tuition');
  Route::get('students/{id}/tuitions','StudentController@tuitions');
  Route::get('students/{id}/announcements','StudentController@show');
  Route::get('students/{id}/comments','StudentController@show_comment_page');
  Route::get('students/{id}/memos','StudentController@show_memo_page');
  Route::get('students/{id}/milestones','StudentController@show_milestone_page');
  Route::get('students/{id}/tasks','StudentController@show_task_page');
  Route::get('students/{id}/school_grades','StudentController@show_school_grade_page');
  Route::get('students/{id}/exams','StudentController@show_exam_page');
  Route::get('students/{id}/exams/{exam_id}','StudentController@show_exam_result_page');



  Route::get('teachers/{id}/calendar','TeacherController@calendar');
  Route::get('teachers/{id}/schedule','TeacherController@schedule');
  Route::get('teachers/{id}/emergency_lecture_cancel','TeacherController@emergency_lecture_cancel');
  Route::get('teachers/{id}/recess','TeacherController@recess');
  Route::get('teachers/{id}/resume','TeacherController@resume');
  Route::get('teachers/{id}/tuition','TeacherController@tuition');
  Route::get('teachers/{id}/ask','TeacherController@ask');
  Route::get('teachers/{id}/ask/create','TeacherController@ask_create_page');
  Route::post('teachers/{id}/ask','TeacherController@ask_create');
  Route::get('teachers/{id}/ask/{ask_id}','TeacherController@ask_details');
  Route::get('teachers/{id}/ask/{ask_id}/edit','TeacherController@ask_edit');
  Route::put('teachers/{id}/ask/{ask_id}','TeacherController@ask_update');
  Route::get('teachers/{id}/announcements','TeacherController@announcements');

  Route::get('managers/{id}/calendar','ManagerController@calendar');
  Route::get('managers/{id}/schedule','ManagerController@schedule');
  Route::get('managers/{id}/unsubscribe','ManagerController@unsubscribe');
  Route::get('managers/{id}/recess','ManagerController@recess');
  Route::get('managers/{id}/resume','ManagerController@resume');
  Route::get('managers/{id}/tuition','ManagerController@tuition');
  Route::get('managers/{id}/ask','ManagerController@ask');
  Route::get('managers/{id}/ask/create','ManagerController@ask_create_page');
  Route::post('managers/{id}/ask','ManagerController@ask_create');
  Route::get('managers/{id}/ask/{ask_id}','ManagerController@ask_details');
  Route::get('managers/{id}/ask/{ask_id}/edit','ManagerController@ask_edit');
  Route::put('managers/{id}/ask/{ask_id}','ManagerController@ask_update');
  Route::get('managers/{id}/announcements','ManagerController@announcements');

  Route::get('parents/{id}/unsubscribe','StudentParentController@unsubscribe');
  Route::get('parents/{id}/recess','StudentParentController@recess');
  Route::get('parents/{id}/late_arrival','StudentParentController@late_arrival');
  Route::get('parents/{id}/students','StudentParentController@get_charge_students');
  Route::get('parents/{id}/ask','StudentParentController@ask');
  Route::get('parents/{id}/ask/create','StudentParentController@ask_create_page');
  Route::post('parents/{id}/ask','StudentParentController@ask_create');
  Route::get('parents/{id}/ask/{ask_id}','StudentParentController@ask_details');
  Route::get('parents/{id}/ask/{ask_id}/edit','StudentParentController@ask_edit');
  Route::put('parents/{id}/ask/{ask_id}','StudentParentController@ask_update');
  Route::get('parents/{id}/announcements','StudentParentController@announcements');



  Route::get('students/{id}/calendar_settings','StudentController@calendar_settings');
  Route::get('teachers/{id}/calendar_settings','TeacherController@calendar_settings');
  Route::get('managers/{id}/calendar_settings','ManagerController@calendar_settings');

  Route::resource('ask_comments','AskCommentController');
  Route::get('asks/{ask_id}/comments/create','AskCommentController@comment_create');
  Route::get('asks/{ask_id}/comments/{id}/edit','AskCommentController@comment_edit');


  Route::resource('student_groups','StudentGroupController');
  Route::get('api_student_groups/{teacher_id}','StudentGroupController@api_index');
  Route::get('teachers/{teacher_id}/student_groups','StudentGroupController@teacher_index');
  Route::get('teachers/{teacher_id}/student_groups/create','StudentGroupController@teacher_create');
  /*
  Route::get('teachers/{teacher_id}/calendar_settings','UserCalendarSettingController@teacher_index');
  Route::get('teachers/{teacher_id?}/calendar_settings/create','UserCalendarSettingController@teacher_create');
  Route::get('teachers/{teacher_id?}/calendar_settings/{id}','UserCalendarSettingController@teacher_show');
  Route::get('teachers/{teacher_id?}/calendar_settings/{id}/edit','UserCalendarSettingController@teacher_edit');
  Route::get('teachers/{teacher_id}/calendars','UserCalendarController@teacher_index');
  Route::get('teachers/{teacher_id?}/calendars/create','UserCalendarController@teacher_create');
  Route::get('teachers/{teacher_id?}/calendars/{id}','UserCalendarController@teacher_show');
  Route::get('teachers/{teacher_id?}/calendars/{id}/edit','UserCalendarController@teacher_edit');
  */

  Route::get('ask_daily_proc/{d?}','AskController@daily_proc');
  Route::get('asks/{id}/status_update/{status}','AskController@status_update_page');
  Route::put('asks/{id}/status_update/{status}','AskController@status_update');
  Route::get('asks/{ask_id}/hope_to_join','AskController@hope_to_join_page');
  Route::get('asks/{ask_id}/agreement','AskController@agreement_page');

  Route::get('asks/{ask_id}/commit','AskController@commit_page');
  Route::get('asks/{id}/edit_date','AskController@edit_date');

  Route::resource('asks','AskController');


  Route::get('api_tuition','TuitionController@get_api_tuition');
  Route::resource('tuitions','TuitionController');
  Route::resource('faqs','FaqController');
  Route::get('faqs/{id}/page','FaqController@page');
  Route::get('home', 'HomeController@index')->name('home');
  Route::get('recess', 'HomeController@recess');
  Route::get('unsubscribe', 'HomeController@unsubscribe');
  Route::get('late_arrival', 'HomeController@late_arrival');

  //教科書を選択する画面
  Route::get('examinations', 'TextbookController@examination_textbook');
  //目次を選択する画面
  Route::get('examinations/{textbook_id}', 'TextbookChapterController@examination_chapter');
  //問題ページを表示する画面(question_idがない場合はカレントの問題を表示）
  Route::get('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@examination');
  Route::post('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@start_examination');
  //Route::redirect('examinations/{textbook_id}/{chapter_id}/{question_id}', '/examinations/{textbook_id}/{chapter_id}', 301);
  Route::post('examinations/{textbook_id}/{chapter_id}/{question_id}', 'UserAnswerController@answer');
  Route::get('messages/create','MessageController@create');
  Route::post('messages/create','MessageController@store');
  Route::get('messages/{id}/details','MessageController@details');
  Route::get('messages/{id}/reply','MessageController@reply');
  Route::post('messages/{id}/reply','MessageController@store');
  Route::get('messages','MessageController@list');
  Route::get('parents/{id}/messages', 'StudentParentController@message_list');
  Route::get('teachers/{id}/messages', 'TeacherController@message_list');
  Route::get('managers/{id}/messages', 'ManagerController@message_list');


  Route::resource('tasks','TaskController');
  Route::resource('task_reviews','TaskReviewController');

  //生徒画面に取り込み
  //Route::get('students/{id}/tasks', 'StudentController@task_list');

  Route::get('tasks/{id}/detail_dialog', 'TaskController@detail_dialog');
  Route::get('tasks/{id}/new', 'TaskController@show_new_page');
  Route::put('tasks/{id}/new', 'TaskController@new');
  Route::get('tasks/{id}/cancel', 'TaskController@show_cancel_page');
  Route::put('tasks/{id}/cancel', 'TaskController@cancel');
  Route::get('tasks/{id}/progress', 'TaskController@show_progress_page');
  Route::put('tasks/{id}/progress', 'TaskController@progress');
  Route::get('tasks/{id}/done', 'TaskController@show_done_page');
  Route::put('tasks/{id}/done', 'TaskController@done');
  Route::get('tasks/{id}/review', 'TaskController@show_review_page');
  Route::put('tasks/{id}/review', 'TaskController@review');
  Route::post('task_comments/create', 'TaskCommentController@store');

  Route::get('curriculums/get_select_list','CurriculumController@get_select_list');
  Route::get('curriculums/name/{name}', 'CurriculumController@name_check');
  Route::resource('curriculums','CurriculumController');
  Route::get('curriculums/{id}/delete', 'CurriculumController@delete');

  Route::resource('subjects','SubjectController');
  Route::get('subjects/{id}/delete', 'SubjectController@delete');

  Route::post('agreements/{id}/admission','AgreementController@admission_mail_send');
  Route::resource('agreements', 'AgreementController');
  Route::get('agreements/{id}/delete', 'AgreementController@delete');
  Route::get('agreements/{id}/ask/{method}', 'AgreementController@ask_page');
  Route::resource('agreement_statements', 'AgreementStatementController');
  Route::get('agreement_statements/{id}/delete', 'AgreementStatementController@delete');

  // 学校関連
  Route::resource('schools', 'SchoolController');
  Route::resource('school_textbooks', 'SchoolTextbookController');
});
Route::get('token_test/{key}','Controller@token_test');
Route::get('test','Controller@test');
Route::resource('maillogs','MailLogController');
Route::resource('actionlogs','ActionLogController');
