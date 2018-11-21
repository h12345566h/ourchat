<?php

namespace App\Services;

use App\Services\EchoTokenService as EchoTokenService;
use App\Services\BaseService as BaseService;
use App\Chat as ChatEloquent;
use App\Message as MessageEloquent;
use App\User as UserEloquent;
use App\ChatMember as ChatMemberEloquent;
use App\User;
use DB;
use Image;
use Storage;

class MessageService
{
    public $echoTokenService;
    public $baseService;

    public function __construct()
    {
        $this->echoTokenService = new EchoTokenService();
        $this->baseService = new BaseService();
    }


    public function sendMessage($messageData)
    {
        $CMCheck = ChatMemberEloquent::where('account', $messageData['account'])
            ->where('chat_id', $messageData['chat_id'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            if (trim($messageData['content']) == "") {
                return '請輸入訊息';
            } else {
                $message = MessageEloquent::create($messageData);

                //推撥
                $getUser = ChatMemberEloquent::where('chat_id', $messageData['chat_id'])
                    ->where('status', 2)
                    ->select('account')->get();
                $plucked = $getUser->pluck('account')->toarray();

                $userName = UserEloquent::find($messageData['account']);
                $chatName = ChatEloquent::find($messageData['chat_id']);
                $notice['account'] = $plucked;

                $push_data['message_id'] = $message->message_id;
                $push_data['content'] = $messageData['content'];
                $push_data['type'] = $messageData['type'];
                $push_data['account'] = $messageData['account'];
                $push_data['created_at'] = $this->baseService->setTime($message->created_at);
                $push_data['name'] = $userName->name;
                $push_data['profile_pic'] = $userName->profile_pic;
                $push_data['chat_id'] = $messageData['chat_id'];

                $notice['push_data'] = $push_data;
                if (strlen($messageData['content']) > 16)
                    $content = substr($messageData['content'], 0, 16) . "...";
                else
                    $content = $messageData['content'];
                $notice['simple'] = $userName->name . ' 在 ' . $chatName->chat_name . '：' . $content;
                //需做一些處理
                $resData = $this->echoTokenService->echo($notice);
                return $push_data;
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
            $dataList = $sql->take(30)->get();

            $this->baseService->setAllTime($dataList);

            return $dataList;
        } else {
            return '此聊天室無此使用者';
        }
    }

    public function uploadImg($file, $chat_id, $account)
    {
        $CMCheck = ChatMemberEloquent::where('account', $account)
            ->where('chat_id', $chat_id)
            ->where('status', 2)->first();
        if ($CMCheck) {
//            $filesName = [];
//            if (is_array($files)) {
//            foreach ($files as $file) {
            $newFileName = date("YmdHis", time()) . '___' . rand(1000, 9999) . '___' . $file->getClientOriginalName();
            if (strlen($newFileName) > 200)
                return '0';
            $image = Image::make($file);
            $image->resize(350, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save('images/Image/' . $newFileName);
//            array_push($filesName, $newFileName);
//            }
            return $newFileName;
//            } else {
//                return '002錯誤';
//            }
        } else {
            return '您並非該群組成員';
        }
    }
}