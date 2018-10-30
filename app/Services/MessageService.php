<?php

namespace App\Services;

use App\Services\BaseService as BaseService;
use App\Message as MessageEloquent;
use App\ChatMember as ChatMemberEloquent;
use DB;
use Image;
use Storage;


class MessageService
{
    public function sendMessage($messageData)
    {
        $CMCheck = ChatMemberEloquent::where('account', $messageData['account'])
            ->where('chat_id', $messageData['chat_id'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            if (trim($messageData['content']) == "") {
                return '請輸入訊息';
            } else {
                MessageEloquent::create($messageData);
                //推撥
                return '';
            }
        } else {
            return '此聊天室無此使用者';
        }

    }

    public function getMessage($messageData)
    {

        $CMCheck = ChatMemberEloquent::where('account', $messageData['account'])
            ->where('chat_id', $messageData['chat_id'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            $sql = DB::table('messages')->where('chat_id', $messageData['chat_id'])
                ->select('messages.message_id', 'messages.content', 'messages.type', 'messages.account', 'messages.created_at', 'user.name', 'user.profile_pic')
                ->join('user', 'messages.account', '=', 'user.account');

            if (array_key_exists('message_id', $messageData))
                $sql->where('message_id', '<', $messageData['message_id']);
            $sql->orderBy('messages.message_id', 'desc');
            $Data = $sql->get();

            $baseService = new BaseService();
            foreach ($Data as $item) {
                $timeDistance = $baseService->timeDistance($item->created_at);
                $item->created_at = $timeDistance;
            }

            return $Data;
        } else {
            return '此聊天室無此使用者';
        }
    }

    public function uploadImg($files)
    {
        $filesName = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $newFileName = date("YmdHis", time()) . '___' . rand(1000, 9999) . '___' . $file->getClientOriginalName();
                if (strlen($newFileName) > 200)
                    return '0';
                $image = Image::make($file);
                $image->resize(350, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save('images/Image/' . $newFileName);
                array_push($filesName, $newFileName);
            }
            return $filesName;
        } else {
            return '002錯誤';
        }

    }
}