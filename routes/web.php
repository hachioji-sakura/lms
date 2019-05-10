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
//indexページをログインにする
Route::redirect('/', '/login', 301);
Route::get('managers/login','ManagerController@login');

Route::get('auth','AuthController@auth');
Route::get('test','UserCalendarController@test');
Route::post('rest/test2','RestController@test');

Route::get('users/email/{email}','UserController@email_check');
Route::get('forget','AuthController@forget');
Route::post('forget','AuthController@reset_mail');

Route::get('password','UserController@password');
Route::post('password','UserController@password_update');
Route::get('password/setting','AuthController@password_setting');
Route::post('password/setting','AuthController@password_settinged');

Route::get('auth/mail','AuthController@mail_send');
//Auth::routesのログアウトは、postのためgetのルーティングを追加
Route::get('logout','Auth\LoginController@logout');

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
Route::resource('parents','StudentParentController');

Route::resource('calendar_settings','UserCalendarSettingController');

Route::resource('calendar_members','UserCalendarMemberController');

Route::get('calendar/{user_id}','UserCalendarController@show_calendar');

Route::get('calendars/{id}/api_test','UserCalendarController@api_test');
Route::get('calendars/{id}/{status}','UserCalendarController@status_update_page');
Route::put('calendars/{id}/{status}','UserCalendarController@status_update');



/*
Route::get('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_page_no_login');
Route::put('calendars/{id}/{status}/{user_id}','UserCalendarController@status_update_no_login');
*/
Route::get('api_calendars/{user_id?}/{from_date?}/{to_date?}','UserCalendarController@api_index');
Route::resource('calendars','UserCalendarController');


Route::get('trials/{id}/to_calendar','TrialController@to_calendar');
Route::post('trials/{id}/to_calendar','TrialController@to_calendar_confirm');
Route::get('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting');
Route::post('trials/{id}/to_calendar_setting','TrialController@to_calendar_setting_update');
Route::get('trials/{id}/admission','TrialController@admission_mail');
Route::post('trials/{id}/admission','TrialController@admission_mail_send');
Route::put('trials/{id}/admission','TrialController@admission_submit');
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


Route::get('students/{id}/agreement','StudentController@agreement_page');
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
Route::get('teachers/{id}/calendar','TeacherController@calendar');
Route::get('teachers/{id}/schedule','TeacherController@schedule');

Route::get('home', 'HomeController@index')->name('home');

//教科書を選択する画面
Route::get('examinations', 'TextbookController@examination_textbook');
//目次を選択する画面
Route::get('examinations/{textbook_id}', 'TextbookChapterController@examination_chapter');
//問題ページを表示する画面(question_idがない場合はカレントの問題を表示）
Route::get('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@examination');
Route::post('examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@start_examination');
//Route::redirect('examinations/{textbook_id}/{chapter_id}/{question_id}', '/examinations/{textbook_id}/{chapter_id}', 301);
Route::post('examinations/{textbook_id}/{chapter_id}/{question_id}', 'UserAnswerController@answer');


// 送信メール本文のプレビュー
Route::get('sample/mailable/preview', function () {
  return new App\Mail\SampleNotification();
});
