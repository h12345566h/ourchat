<?php

namespace App\Services;

use App\User as UserEloquent;
use App\Chat as ChatEloquent;
use App\ChatMember as ChatMemberEloquent;
use Image;
use Storage;
use DB;

class ChatService
{
    public function getChat($chatId)
    {
        $chat = DB::table('chats')->where('chats.chat_id', $chatId)
            ->select('chats.chat_id', 'chats.chat_name', 'chats.profile_pic as chat_profile_pic', 'messages.content', 'messages.type', 'messages.account', 'messages.created_at', 'user.name', 'user.profile_pic as user_profile_pic')
            ->leftJoin('messages', 'messages.message_id', '=', DB::raw('(select message_id from messages where messages.chat_id = chats.chat_id order by created_at desc limit 1)'))
            ->leftJoin('users', 'messages.account','=','users.account')
            ->first();
        return $chat;
    }

    public function createChat($chatData)
    {
        $userCheck = UserEloquent::find($chatData['account']);
        if ($userCheck) {
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

    public function searchChat($chatData)
    {
        $keyword = '%' . $chatData['keyword'] . '%';

        $dataList = DB::table('chats')->where('chats.chat_name', 'like', $keyword)
            ->select('chats.chat_id', 'chats.chat_name', 'chats.created_at', 'chats.creator', 'chats.profile_pic as chat_profile_pic', 'users.name as creator_name', 'users.profile_pic as creator_profile_pic', 'chat_members.status')
            ->join('users', 'chats.creator', '=', 'users.account')
            ->leftJoin('chat_members', function ($join) use ($chatData) {
                $join->on('chat_members.chat_id', '=', 'chats.chat_id')
                    ->where('chat_members.account', '=', $chatData['account']);
            })
            ->orderBy('chats.chat_id', 'desc')
            ->get();
        $baseService = new BaseService();
        $baseService->setAllTime($dataList);
        return $dataList;
    }

    public function updateChatProfilePic(\Illuminate\Http\UploadedFile $file, $chat_id, $account)
    {
        $CMCheck = ChatMemberEloquent::where('account', $account)
            ->where('chat_id', $chat_id)
            ->where('status', 2)->first();
        if ($CMCheck) {
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
        } else {
            return '您並非該群組成員';
        }

    }

    public function editChat($chatData)
    {
        $CMCheck = ChatMemberEloquent::where('account', $chatData['account'])
            ->where('chat_id', $chatData['chat_id'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            $Chat = ChatEloquent::find($chatData['chat_id']);
            $Chat->chat_name = $chatData['chat_name'];
            $Chat->save();
            return '';
        } else {
            return '您不是該群組成員';
        }

    }
}