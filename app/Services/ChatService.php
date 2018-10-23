<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2018/10/3
 * Time: 15:31
 */

namespace App\Services;

use App\User as UserEloquent;
use App\Chat as ChatEloquent;
use App\ChatMember as ChatMemberEloquent;
use Image;
use Storage;


class ChatService
{
    public function createChat($chatData)
    {
        $UserCheck = UserEloquent::find($chatData['account']);
        if ($UserCheck) {
            $ChatCheck = ChatEloquent::where('creator', $chatData['account'])
                ->where('chat_name', $chatData['chat_name'])->first();
            if ($ChatCheck) {
                return '你已有相同名稱群組';
            } else {
                $chatData['creator'] = $chatData['account'];
                $chat = ChatEloquent::create($chatData);

                $chatMemberData['chat_id'] = $chat->chat_id;
                $chatMemberData['account'] = $chatData['account'];
                $chatMemberData['status'] = 2;
                ChatMemberEloquent::create($chatMemberData);
                return '';
            }
        } else {
            return '創建者帳號有誤';
        }
    }


    public function getChat($chatData)
    {
        $keyword = $chatData['keyword'];
        $getChatData = ChatEloquent::where('chat_name', 'like', "%$keyword%")
            ->orderByDesc('chat_id')
            ->take(15)
            ->get();
        return $getChatData;
    }

    public function updateChatProfilePic(\Illuminate\Http\UploadedFile $file, $chat_id)
    {
        $newFileName = date("YmdHis", time()) . '___' . rand(1000, 9999) . '___' . $file->getClientOriginalName();
        if (strlen($newFileName) > 200)
            return '0';
        $image = Image::make($file);
        $image->resize(350, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save('images/ChatProfilePic/' . $newFileName);
        $Chat = ChatEloquent::find($chat_id);
        if ($Chat->profile_pic) {
            if (file_exists('images/ChatProfilePic/' . $Chat->profile_pic)) {
                unlink('images/ChatProfilePic/' . $Chat->profile_pic);
            }
        }
        $Chat->profile_pic = $newFileName;
        $Chat->save();
        return $newFileName;
    }
}