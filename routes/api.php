<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'DisplayChinese'], function () {

    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');

    Route::group(['middleware' => ['jwt_auth', 'RefreshToken']], function () {
        /*  user  */
        Route::get('getUserData', 'UserController@getUserData');
        Route::get('logout', 'UserController@logout');
        Route::post('updateProfilePic', 'UserController@updateProfilePic');
        /*  message  */
        Route::post('sendMessage', 'MessageController@sendMessage');
        Route::post('uploadImg', 'MessageController@uploadImg');
        Route::get('getMessage', 'MessageController@getMessage');
        /*  chatmember  */
        Route::post('addCM', 'ChatMemberController@addCM');
        Route::post('inviteCM', 'ChatMemberController@inviteCM');
        Route::post('refuseCM', 'ChatMemberController@refuseCM');
        Route::post('acceptCM', 'ChatMemberController@acceptCM');
        Route::post('quitChat', 'ChatMemberController@quitChat');
        Route::get('getCM', 'ChatMemberController@getCM');
        Route::get('getUncheckCM', 'ChatMemberController@getUncheckCM');
        Route::get('getMyInvite', 'ChatMemberController@getMyInvite');
        Route::get('getMyChat', 'ChatMemberController@getMyChat');

        /*  caht  */
        Route::post('createChat', 'ChatController@createChat');
        Route::get('getChat', 'ChatController@getChat');
        Route::post('updateChatProfilePic', 'ChatController@updateChatProfilePic');

        /*  Echo  */
        Route::post('createEchoToken', 'EchoController@createEchoToken');
    });
});