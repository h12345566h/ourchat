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
                'message' => 'required|max:190',
                'chat_id' => 'required|integer',
                'type' => 'required|integer',
            ],
            [
                'message.max' => '訊息不可超過190字',
                'message.required' => '請輸入訊息',
                'chat_id.*' => '001錯誤',
                'type.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json(['errorMessage' => $objValidator->errors()->all()], 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->messageService->sendMessage($messageData);
        if ($result != '')
            return response()->json(['errorMessage' => $result], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);

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
            ],
            [
                'chat_id.*' => '001錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json(['errorMessage' => $objValidator->errors()->all()], 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->messageService->getMessage($messageData);
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
                'Image' => 'required|array'
            ],
            [
                'chat_id.*' => '001錯誤',
                'Image.required' => '請上傳圖片'
            ]
        );
        if ($objValidator->fails())
            return response()->json(['errorMessage' => $objValidator->errors()->all()], 400, [], JSON_UNESCAPED_UNICODE);
        //圖片陣列內圖片驗證
        $imageRules = array(
            'Image' => 'mimes:jpeg,bmp,png'
        );
        $imageMessage = array(
            'mimes' => '圖檔格式錯誤(副檔名須為jpg ,jpeg, png, bmp)',
            'required' => '請上傳圖片'
        );
        foreach ($input['Image'] as $image) {
            $images = array('Image' => $image);
            $imageValidator = Validator::make($images, $imageRules, $imageMessage);
            if ($imageValidator->fails()) {
                return response()->json(['errorMessage' => $imageValidator->errors()->all()], 400, [], JSON_UNESCAPED_UNICODE);
            }
        }
        $files = $input['Image'];
        $newFileName = $this->messageService->uploadImg($files);
        if ($newFileName == '002錯誤')
            return response()->json(['errorMessage' => '002錯誤'], 400, [], JSON_UNESCAPED_UNICODE);
        else if ($newFileName == '0')
            return response()->json(['errorMessage' => '檔名過長'], 400, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json($newFileName, 200, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

}