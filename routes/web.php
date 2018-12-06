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
Route::get('users/email/{email}','UserController@email_check');
Route::get('password','UserController@password');
Route::post('password','UserController@password_update');

Route::get('auth/mail','AuthController@mail_send');
//Auth::routesのログアウトは、postのためgetのルーティングを追加
Route::get('logout','Auth\LoginController@logout');

Route::resource('images','ImageController');
//Route::resource('rest','RestController', ['only' => ['index', 'create', 'edit', 'store', 'destroy']]);
Route::get('import/{object?}','ImportController@index');

Route::resource('attributes','GeneralAttributeController');
Route::resource('milestones','MilestoneController');
Route::resource('comments','CommentController');

/*
Route::resource('publisher','PublisherController');
Route::resource('textbooks','TextbookController');
*/
Route::resource('students','StudentController');
Route::resource('managers','ManagerController');
Route::resource('teachers','TeacherController');
Route::resource('comments','CommentController');
Route::resource('user_examinations','UserExaminationController');

Route::post('students/{id}/comments/create','CommentController@student_comments_store');
Route::post('students/{id}/icon','ImageController@icon_change');
Route::post('students/{student_id}/icon','ImageController@icon_change');
Route::get('/home', 'HomeController@index')->name('home');

//教科書を選択する画面
Route::get('/examinations', 'TextbookController@examination_textbook');
//目次を選択する画面
Route::get('/examinations/{textbook_id}', 'TextbookChapterController@examination_chapter');
//問題ページを表示する画面(question_idがない場合はカレントの問題を表示）
Route::get('/examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@examination');
Route::post('/examinations/{textbook_id}/{chapter_id}', 'UserExaminationController@start_examination');
//Route::redirect('/examinations/{textbook_id}/{chapter_id}/{question_id}', '/examinations/{textbook_id}/{chapter_id}', 301);
Route::post('/examinations/{textbook_id}/{chapter_id}/{question_id}', 'UserAnswerController@answer');
