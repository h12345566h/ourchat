<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;
use Auth;
use Validator;

class MessageController extends Controller
{
    public $messageService;

    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    //region send 文字:1 圖片:2
    public function sendMessage(Request $request)
    {
        $messageData = $request->all();
        $objValidator = Validator::make(
            $messageData,
            [
                'content' => 'required|max:190',
                'chat_id' => 'required|integer',
                'type' => 'required|integer',
            ],
            [
                'content.max' => '訊息不可超過190字',
                'content.required' => '請輸入訊息',
                'chat_id.*' => '001錯誤',
                'type.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->messageService->sendMessage($messageData);
        if (is_string($result))
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region getMessage
    public function getMessage(Request $request)
    {
        $messageData = $request->all();
        $objValidator = Validator::make(
            $messageData,
            [
                'chat_id' => 'required|integer',
                'message_id' => 'integer',
            ],
            [
                'chat_id.*' => '001錯誤',
                'message_id.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->messageService->getMessage($messageData);
        if (is_string($result))
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    // region 上傳圖片
    public function uploadImg(Request $request)
    {
        $input = $request->all();
        $objValidator = Validator::make(
            $request->all(),
            [
                'chat_id' => 'required|integer',
                'message_image' => 'required'
            ],
            [
                'chat_id.*' => '001錯誤',
                'message_image.required' => '請上傳圖片'
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
        //圖片陣列內圖片驗證
        $imageRules = array(
            'message_image' => 'mimes:jpeg,bmp,png'
        );
        $imageMessage = array(
            'mimes' => '圖檔格式錯誤(副檔名須為jpg ,jpeg, png, bmp)',
            'required' => '請上傳圖片'
        );
        foreach ($input['message_image'] as $image) {
            $images = array('image' => $image);
            $imageValidator = Validator::make($images, $imageRules, $imageMessage);
            if ($imageValidator->fails()) {
                return response()->json($imageValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
            }
        }
        $files = $input['message_image'];
        $newFileName = $this->messageService->uploadImg($files, $request->input('chat_id'), $request->input('account'));
        if ($newFileName == '002錯誤')
            return response()->json(['002錯誤'], 400, [], JSON_UNESCAPED_UNICODE);
        else if ($newFileName == '0')
            return response()->json(['檔名過長'], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($newFileName, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

}