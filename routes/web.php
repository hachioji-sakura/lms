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

Route::get('auth','AuthController@auth');
Route::get('test','AuthController@test');
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

Route::get('api_attributes/{select_key?}','GeneralAttributeController@api_index');
Route::resource('attributes','GeneralAttributeController');
Route::resource('milestones','MilestoneController');
Route::get('comments/{id}/publiced','CommentController@publiced_page');
Route::put('comments/{id}/publiced','CommentController@publiced');
Route::resource('comments','CommentController');
Route::resource('parents','StudentParentController');

Route::get('calendars/test/{status}','UserCalendarController@test');
Route::get('calendars/{id}/{status}','UserCalendarController@status_update_page');
Route::put('calendars/{id}/{status}','UserCalendarController@status_update');
Route::get('api_calendars/{user_id?}/{from_date?}/{to_date?}','UserCalendarController@api_index');
Route::resource('calendars','UserCalendarController');

Route::get('api_lectures','LectureController@api_index');
Route::resource('lectures','LectureController');

/*
Route::resource('publisher','PublisherController');
Route::resource('textbooks','TextbookController');
*/
Route::get('entry','StudentParentController@entry');
Route::post('entry','StudentParentController@entry_store');
Route::get('register','StudentParentController@register');
Route::post('register','StudentParentController@register_update');

Route::resource('parents','StudentParentController');
Route::resource('students','StudentController');
Route::resource('managers','ManagerController');
Route::resource('teachers','TeacherController');
Route::resource('comments','CommentController');
Route::resource('user_examinations','UserExaminationController');
Route::post('icon','ImageController@icon_change');


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
