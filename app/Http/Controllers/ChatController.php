<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Auth;
use Illuminate\Http\Request;
use Validator;

class ChatController extends Controller
{
    public $chatService;

    public function __construct()
    {
        $this->chatService = new ChatService();
    }

    //region 建立聊天室
    public function createChat(Request $request)
    {
        $chatData = $request->all();
        $objValidator = Validator::make(
            $chatData,
            [
                'chat_name' => 'required|string ',
            ],
            [
                'chat_name.required' => '請輸入聊天室名稱',
                'chat_name.string' => '聊天室名稱須為字串'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->chatService->createChat($chatData);
        if ($result != '')
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion


    //region 搜尋聊天室
    public function getChat(Request $request)
    {
        $chatData = $request->all();
        $objValidator = Validator::make(
            $chatData,
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

        $result = $this->chatService->getChat($chatData);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 設定聊天室大頭貼
    public function updateChatProfilePic(Request $request)
    {
        $objValidator = Validator::make(
            $request->all(),
            [
                'profile_pic' => 'required|mimes:jpeg,bmp,png',
                'chat_id' => 'required|integer',
            ],
            [
                'profile_pic.mimes' => '圖檔格式錯誤(副檔名須為jpg ,jpeg, png, bmp)',
                'profile_pic.required' => '請上傳圖片',
                'chat_id.*' => '錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        $file = $request->file('profile_pic');
        if (!$file->isValid()) {
            return response()->json(['保存圖片失敗'], 400, [], JSON_UNESCAPED_UNICODE);
        }
        $newFileName = $this->chatService->updateChatProfilePic($file, $request->input('chat_id'));
        if ($newFileName == '0')
            return response()->json(['檔名過長'], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($newFileName, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion
}