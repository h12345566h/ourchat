<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2018/10/3
 * Time: 15:31
 */

namespace App\Services;

use App\Message as MessageEloquent;
use App\ChatMember as ChatMemberEloquent;
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
            if (!empty($messageData['message'])) {
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
            if ($messageData['message_id']) {
                $MessageArr = MessageEloquent::where('chat_id', $messageData['chat_id'])
                    ->where('message_id', '<', $messageData['message_id'])
                    ->orderByDesc('created_at')
                    ->take(15)
                    ->with(['user' => function ($query) {
                        $query->select(['account', 'name', 'profile_pic']);
                    }])
                    ->with(['chat' => function ($query) {
                        $query->select(['chat_id', 'chat_name', 'creator', 'profile_pic']);
                    }])
                    ->get();
                return $MessageArr;
            } else {
                $MessageArr = MessageEloquent::where('chat_id', $messageData['chat_id'])
                    ->orderByDesc('created_at')
                    ->take(15)
                    ->with(['user' => function ($query) {
                        $query->select(['account', 'name', 'profile_pic']);
                    }])
                    ->with(['chat' => function ($query) {
                        $query->select(['chat_id', 'chat_name', 'creator', 'profile_pic']);
                    }])
                    ->get();
                return $MessageArr;
            }

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