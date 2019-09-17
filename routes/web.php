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
Auth::routes();

if(isset($_GET["locale"]) && !empty($_GET["locale"])){
  App::setLocale($_GET["locale"]);
}
//indexページをログインにする
Route::redirect('/', '/login', 301);
Route::get('token_test/{key}','Controller@token_test');

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

Route::post('upload_images','ImageController@upload_images');
Route::resource('images','ImageController');
Route::resource('rest','RestController', ['only' => ['index', 'create', 'edit', 'store', 'destroy']]);
Route::get('import/{object?}','ImportController@index');
Route::post('import/{object?}','ImportController@import');
Route::put('sync/{object?}','ImportController@sync');

Route::get('api_attributes/{select_key?}','GeneralAttributeController@api_index');
Route::resource('attributes','GeneralAttributeController');
Route::resource('milestones','MilestoneController');
Route::get('comments/{id}/publiced','CommentController@publiced_page');
Route::put('comments/{id}/publiced','CommentController@publiced');
Route::resource('comments','CommentController');

Route::resource('calendar_settings','UserCalendarSettingController');
Route::get('calendar_settings/{id}/to_calendar','UserCalendarSettingController@to_calendar_page');
Route::post('calendar_settings/{id}/to_calendar','UserCalendarSettingController@to_calendar');
Route::post('api_setting_to_calendar/{id?}','UserCalendarSettingController@api_setting_to_calendar');

Route::resource('calendar_members','UserCalendarMemberController');
Route::put('calendar_members/{id}/rest_type','UserCalendarMemberController@rest_type_update');

Route::get('calendars/{id}/status_update/{status}','UserCalendarController@status_update_page');
Route::put('calendars/{id}/status_update/{status}','UserCalendarController@status_update');



/*
Route::get('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_page_no_login');
Route::put('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_no_login');
*/
Route::get('api_calendars/{user_id?}/{from_date?}/{to_date?}','UserCalendarController@api_index');
Route::get('api_english_group_calendars','UserCalendarController@api_english_group');
Route::resource('calendars','UserCalendarController');


Route::get('trials/{id}/to_calendar','TrialController@to_calendar');
Route::post('trials/{id}/to_calendar','TrialController@to_calendar_confirm');
Route::get('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting');
Route::post('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting_update');
Route::get('trials/{id}/admission','TrialController@admission_mail');
Route::post('trials/{id}/admission','TrialController@admission_mail_send');
Route::put('trials/{id}/admission','TrialController@admission_submit');
Route::get('trials/{id}/commit','TrialController@admission');

/*
Route::get('trials/{id}/{status}','TrialController@status_update_page');
Route::put('trials/{id}/{status}','TrialController@status_update');
*/
Route::resource('trials','TrialController');
Route::get('entry','TrialController@trial');
Route::post('entry','TrialController@trial_store');


Route::get('api_lectures','LectureController@api_index');
Route::get('api_course','LectureController@api_index');
Route::resource('lectures','LectureController');

/*
Route::resource('publisher','PublisherController');
Route::resource('textbooks','TextbookController');
*/
Route::get('teachers/{id}/month_work/{target_moth?}','TeacherController@month_work');
Route::post('teachers/{id}/month_work','TeacherController@month_work_confirm');
Route::get('teachers/{id}/to_manager','TeacherController@to_manager_page');
Route::post('teachers/{id}/to_manager','TeacherController@to_manager');
Route::get('teachers/{id}/students','TeacherController@get_charge_students');

Route::get('register','StudentParentController@register');
Route::post('register','StudentParentController@register_update');
Route::get('teachers/entry','TeacherController@entry');
Route::post('teachers/entry','TeacherController@entry_store');
Route::get('teachers/register','TeacherController@register');
Route::post('teachers/register','TeacherController@register_update');
Route::get('managers/entry','ManagerController@entry');
Route::post('managers/entry','ManagerController@entry_store');
Route::get('managers/register','ManagerController@register');
Route::post('managers/register','ManagerController@register_update');
Route::get('managers/{id}/month_work/{target_moth?}','ManagerController@month_work');
Route::post('managers/{id}/month_work','ManagerController@month_work_confirm');


Route::get('students/{id}/agreement','StudentController@agreement_page');
Route::get('parents/{id}/agreement','StudentParentController@agreement_page');
Route::get('parents/{id}/delete','StudentParentController@delete_page');
Route::get('students/{id}/delete','StudentController@delete_page');
Route::get('managers/{id}/delete','ManagerController@delete_page');
Route::get('teachers/{id}/delete','TeacherController@delete_page');
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


Route::post('students/{id}/comments/create','CommentController@student_comments_store');
Route::get('students/{id}/calendar','StudentController@calendar');
Route::get('students/{id}/schedule','StudentController@schedule');
Route::get('students/{id}/unsubscribe','StudentController@unsubscribe');
Route::get('students/{id}/recess','StudentController@recess');
Route::get('students/{id}/resume','StudentController@resume');

Route::get('teachers/{id}/calendar','TeacherController@calendar');
Route::get('teachers/{id}/schedule','TeacherController@schedule');
Route::get('teachers/{id}/unsubscribe','TeacherController@unsubscribe');
Route::get('teachers/{id}/recess','TeacherController@recess');
Route::get('teachers/{id}/resume','TeacherController@resume');

Route::get('managers/{id}/calendar','ManagerController@calendar');
Route::get('managers/{id}/unsubscribe','ManagerController@unsubscribe');
Route::get('managers/{id}/recess','ManagerController@recess');
Route::get('managers/{id}/resume','ManagerController@resume');

Route::get('parents/{id}/unsubscribe','StudentParentController@unsubscribe');
Route::get('parents/{id}/recess','StudentParentController@recess');


Route::get('students/{id}/tuition','StudentController@tuition');
Route::get('teachers/{id}/tuition','TeacherController@tuition');
Route::get('teachers/{id}/ask','TeacherController@ask');
Route::get('teachers/{id}/ask','TeacherController@ask');
Route::get('managers/{id}/ask','ManagerController@ask');
Route::get('students/{id}/calendar_settings','StudentController@calendar_settings');
Route::get('teachers/{id}/calendar_settings','TeacherController@calendar_settings');
Route::get('managers/{id}/calendar_settings','ManagerController@calendar_settings');


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
Route::get('asks/{ask_id}/teacher_change','UserCalendarController@teacher_change_page');
Route::get('asks/{ask_id}/agreement','AskController@agreement_page');
Route::resource('asks','AskController');


Route::resource('tuitions','TuitionController');
Route::resource('faqs','FaqController');
Route::get('faqs/{id}/page','FaqController@page');
Route::get('home', 'HomeController@index')->name('home');
Route::get('recess', 'HomeController@recess');
Route::get('unsubscribe', 'HomeController@unsubscribe');

//教科書を選択する画面
Route::get('examinations', 'TextbookController@examination_textbook');
//目次を選択する画面
Route::get('examinations/{textbook_id}', 'TextbookChapterController@examination_chapter');
//問題ページを表示する画面(question_idがない場合はカレントの問題を表示）
Route::get('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@examination');
Route::post('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@start_examination');
//Route::redirect('examinations/{textbook_id}/{chapter_id}/{question_id}', '/examinations/{textbook_id}/{chapter_id}', 301);
Route::post('examinations/{textbook_id}/{chapter_id}/{question_id}', 'UserAnswerController@answer');
