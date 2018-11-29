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

Route::post('students/{student_id}/icon','ImageController@icon_change');
Route::get('/home', 'HomeController@index')->name('home');
