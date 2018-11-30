<?php

namespace App\Http\Controllers;

use App\Services\EchoTokenService;
use Illuminate\Http\Request;
use App\Services\UserService;
use Auth;
use Validator;

error_reporting(0);

class UserController extends Controller
{
    public $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    //region 註冊
    public function register(Request $request)
    {
        $postData = $request->all();
        $objValidator = Validator::make(
            $postData,
            [
                'account' => [
                    'required',
                    'between:6,20',
                    'regex:/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i',
                    'unique:user'
                ],
                'password' => [
                    'required',
                    'between:6,20',
                    'regex:/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i'
                ],
                'name' => 'required|max:20',
                'email' => 'required|email|max:50'
            ],
            [
                'account.required' => '請輸入帳號',
                'account.between' => '帳號需介於6-20字元',
                'account.regex' => '帳號需包含英文數字',
                'account.unique' => '帳號已存在',
                'password.required' => '請輸入密碼',
                'password.between' => '密碼需介於 6-20 字元',
                'password.regex' => '密碼需包含英文數字',
                'name.required' => '請輸入姓名',
                'name.max' => '姓名不可超過 20 字元',
                'email.required' => '請輸入信箱',
                'email.email' => '信箱格式錯誤',
                'email.max' => '信箱不可超過 50 字元'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $this->userService->register($postData);
        return response()->json(Auth::guard()->attempt($postData), 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 修改
    public function editUser(Request $request)
    {
        $postData = $request->all();
        $objValidator = Validator::make(
            $postData,
            [
                'name' => 'required|max:20',
                'email' => 'required|email|max:50'
            ],
            [
                'email.required' => '請輸入信箱',
                'email.email' => '信箱格式錯誤',
                'email.max' => '信箱不可超過 50 字元'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $this->userService->editUser($postData);
        return response()->json('修改成功', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 登入
    public function login(Request $request)
    {
        $postData = $request->only('account', 'password');
        $objValidator = Validator::make(
            $postData,
            [
                'account' => 'required',
                'password' => 'required'
            ],
            [
                'account.required' => '請輸入帳號',
                'password.required' => '請輸入密碼'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $resMessage = $this->userService->login($postData);
        if ($resMessage != '')
            return response()->json([$resMessage], 400);
        return response()->json(Auth::guard()->attempt($postData), 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 修改密碼
    public function passwordChange(Request $request)
    {
        $postData = $request->all();
        $objValidator = Validator::make(
            $postData,
            [
                'account' => 'required',
                'old_password' => 'required',
                'new_password' => 'required',
                'new_password2' => 'required'
            ],
            [
                'account.required' => '請輸入帳號',
                'old_password.required' => '請輸入舊密碼',
                'new_password.required' => '請輸入新密碼',
                'new_password2.required' => '請輸入確認新密碼'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $resMessage = $this->userService->passwordChange($postData);
        if ($resMessage != '') {
            return response()->json([$resMessage], 400);
        } else {
            Auth::guard()->logout();
            $newpostData['account'] = $postData['account'];
            $newpostData['password'] = $postData['new_password'];

            return response()->json(['token' => Auth::guard()->attempt($newpostData)], 200, [], JSON_UNESCAPED_UNICODE);
        }

    }
    //endregion

    // region GET個人資料
    public function getUserData(Request $request)
    {
        $auth = Auth::guard()->user();
        $auth['token'] = $request->input("token");
        return response()->json($auth, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 登出
    public function logout(Request $request)
    {
        $postData = $request->all();
        $objValidator = Validator::make(
            $postData,
            [
                'echo_token' => 'required'
            ],
            [
                'echo_token.required' => 'error'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $echoTokenService = new EchoTokenService();
        $echoTokenService->deleteEchoToken($postData);
        Auth::guard()->logout();
        return response()->json('登出成功', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 設定個人大頭貼
    public function updateProfilePic(Request $request)
    {
        $objValidator = Validator::make(
            $request->all(),
            [
                'profile_pic' => 'required|mimes:jpeg,bmp,png'
            ],
            [
                'mimes' => '圖檔格式錯誤(副檔名須為jpg ,jpeg, png, bmp)',
                'required' => '請上傳圖片'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $file = $request->file('profile_pic');
        if (!$file->isValid()) {
            return response()->json(['保存圖片失敗'], 400, [], JSON_UNESCAPED_UNICODE);
        }
        $newFileName = $this->userService->updateProfilePic($file, $request->input('account'));
        if ($newFileName == '0')
            return response()->json(['檔名過長'], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($newFileName, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 帳號搜尋
    public function searchUser(Request $request)
    {
        $userData = $request->all();
        $objValidator = Validator::make(
            $userData,
            [
                'keyword' => 'string|required',
            ],
            [
                'keyword.string' => '關鍵字須為字串',
                'keyword.required' => '請輸入關鍵字'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->userService->searchUser($userData);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

}