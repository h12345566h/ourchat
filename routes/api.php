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

    /*刷新token*/
//    Route::group(['middleware' => 'RefreshToken'], function () {
//        Route::get('getUserData', 'UserController@getUserData');
//    });

    Route::group(['middleware' => 'jwt_auth'], function () {

        /*  user  */
        Route::post('logout', 'UserController@logout');
        Route::post('updateProfilePic', 'UserController@updateProfilePic');
        Route::get('searchUser', 'UserController@searchUser');
        Route::get('getUserData', 'UserController@getUserData');
        Route::post('editUser', 'UserController@editUser');
        Route::post('passwordChange', 'UserController@passwordChange');
        Route::post('middlewareTest', 'UserController@middlewareTest');

        /*  message  */
        Route::post('sendMessage', 'MessageController@sendMessage');
        Route::post('uploadImg', 'MessageController@uploadImg');
        Route::get('getMessage', 'MessageController@getMessage');
        Route::get('revoke', 'MessageController@revoke');

        /*  chatMember  */
        Route::post('addCM', 'ChatMemberController@addCM');
        Route::post('inviteCM', 'ChatMemberController@inviteCM');
        Route::post('refuseCM', 'ChatMemberController@refuseCM');
        Route::post('acceptCM', 'ChatMemberController@acceptCM');
        Route::post('quitChat', 'ChatMemberController@quitChat');
        Route::get('getCM', 'ChatMemberController@getCM');
        Route::get('getUncheckCM', 'ChatMemberController@getUncheckCM');
        Route::get('getMyInvite', 'ChatMemberController@getMyInvite');
        Route::get('getMyChat', 'ChatMemberController@getMyChat');

        /*  chat  */
        Route::post('createChat', 'ChatController@createChat');
        Route::get('getChat', 'ChatController@getChat');
        Route::get('searchChat', 'ChatController@searchChat');
        Route::post('updateChatProfilePic', 'ChatController@updateChatProfilePic');
        Route::post('editChat', 'ChatController@editChat');

        /*  echo  */
        Route::post('createEchoToken', 'EchoController@createEchoToken');

        /*  report  */
        Route::post('createReport', 'ReportController@createReport');

        /*  black  */
        Route::post('createBlack', 'BlackController@createBlack');
        Route::post('deleteBlack', 'BlackController@deleteBlack');
        Route::get('getMyBlack', 'BlackController@getMyBlack');

    });
});