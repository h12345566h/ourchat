<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatMemberService;
use Auth;
use Validator;

class ChatMemberController extends Controller
{
    public $ChatMemberService;

    public function __construct()
    {
        $this->ChatMemberService = new ChatMemberService();
    }

    //region 自行加入聊天室
    public function addCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
            ],
            [
                'chat_id.*' => '錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->addCM($chatMemberData);
        if ($result != '')
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 邀請加入聊天室
    public function inviteCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
                'inv_account' => 'required',
            ],
            [
                'chat_id.*' => '錯誤',
                'inv_account.required' => '請輸入使用者',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->inviteCM($chatMemberData);
        if ($result != '')
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 接受加入聊天室
    public function acceptCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
                'inv_account' => 'required|string',
            ],
            [
                'chat_id.*' => '錯誤001',
                'inv_account.required' => '請輸入使用者',
                'inv_account.string' => '使用者帳號請輸入字串',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->acceptCM($chatMemberData);
        if ($result != '')
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 拒絕加入聊天室
    public function refuseCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
                'inv_account' => 'required|string',
            ],
            [
                'chat_id.*' => '錯誤001',
                'inv_account.required' => '請輸入使用者',
                'inv_account.string' => '使用者帳號請輸入字串',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->refuseCM($chatMemberData);
        if ($result != '')
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 退出聊天室
    public function quitChat(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
            ],
            [
                'chat_id.*' => '錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->quitChat($chatMemberData);
        if ($result != '')
            return response()->json([$result], 400);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 取得聊天室成員
    public function getCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
            ],
            [
                'chat_id.*' => '錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->getCM($chatMemberData);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 取得未確認成員
    public function getUncheckCM(Request $request)
    {
        $chatMemberData = $request->all();
        $objValidator = Validator::make(
            $chatMemberData,
            [
                'chat_id' => 'required|integer',
            ],
            [
                'chat_id.*' => '錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json([$objValidator->errors()->all()], 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->ChatMemberService->getUncheckCM($chatMemberData);
        if (is_string($result))
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);


    }
    //endregion

    //region 取得我的邀請列
    public function getMyInvite(Request $request)
    {
        $chatMemberData = $request->all();
        $result = $this->ChatMemberService->getMyInvite($chatMemberData);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 取得我的聊天室
    public function getMyChat(Request $request)
    {
        $chatMemberData = $request->all();
        $result = $this->ChatMemberService->getMyChat($chatMemberData);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion
}