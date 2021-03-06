<?php

namespace App\Services;

use App\User as UserEloquent;
use App\Chat as ChatEloquent;
use App\ChatMember as ChatMemberEloquent;
use Image;
use Storage;
use DB;

class ChatMemberService
{
    public function addCM($chatMemberData)
    {
        $ChatCheck = ChatEloquent::find($chatMemberData['chat_id']);
        if ($ChatCheck) {
            $accCheck = UserEloquent::find($chatMemberData['account']);
            if ($accCheck) {
                $CMCheck = ChatMemberEloquent::where('account', $chatMemberData['account'])
                    ->where('chat_id', $chatMemberData['chat_id'])->first();
                if (!$CMCheck) {
                    $chatMemberData['status'] = 0;
                    ChatMemberEloquent::create($chatMemberData);
                    return '';
                } else {
                    if ($CMCheck->status == 0)
                        return '該帳號已申請加入';
                    if ($CMCheck->status == 1)
                        return '該帳號已在邀請名單中';
                    if ($CMCheck->status == 2)
                        return '該帳號已在群組中';
                    return 'error';
                }
            } else {
                return '無此使用者';
            }
        } else {
            return '無此聊天室';
        }
    }

    public function inviteCM($chatMemberData)
    {
        //邀請人Check
        $invCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['account'])
            ->where('status', 2)->first();
        if ($invCheck) {
            //被邀請人UserCheck
            $accCheck = UserEloquent::find($chatMemberData['inv_account']);
            if ($accCheck) {
                //被邀請人CMCheck
                $CMCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
                    ->where('account', $chatMemberData['inv_account'])->first();
                if (!$CMCheck) {
                    $chatMemberData['status'] = 1;
                    $chatMemberData['account'] = $chatMemberData['inv_account'];
                    ChatMemberEloquent::create($chatMemberData);
                    return '';
                } else {
                    return '該成員已被邀請或已在該群組';
                }
            } else {
                return '無此使用者';
            }
        } else {
            return '您並非此聊天室成員';
        }
    }

    public function acceptCM($chatMemberData)
    {
        //被邀請人check
        $CMCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['inv_account'])
            ->whereIn('status', array(0, 1))->first();
        if ($CMCheck) {
            if ($CMCheck->status == 0) {
                //准許者check
                $userCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
                    ->where('account', $chatMemberData['account'])
                    ->where('status', 2)->first();
                if ($userCheck) {
                    $CMCheck->status = 2;
                    $CMCheck->save();
                    return '';
                } else {
                    return '您不是該聊天室成員';
                }
            } else {
                if ($chatMemberData['account'] == $CMCheck->account) {
                    $CMCheck->status = 2;
                    $CMCheck->save();
                    return '';
                }
                return '您的帳號有誤';
            }
        } else {
            return '該聊天室無此邀請或該帳號已是成員';
        }

    }

    public function refuseCM($chatMemberData)
    {
        //被邀請人check
        $CMCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['inv_account'])
            ->whereIn('status', [0, 1])->first();
        if ($CMCheck) {
            if ($CMCheck->status == 0) {
                //准許者check
                $userCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
                    ->where('account', $chatMemberData['account'])
                    ->where('status', 2)->first();
                if ($userCheck) {
                    $CMCheck->delete();
                    return '';
                } else {
                    return '您不是該聊天室成員';
                }
            } else {
                if ($chatMemberData['account'] == $CMCheck->account) {
                    $CMCheck->delete();
                    return '';
                }
                return '您的帳號有誤';
            }
        } else {
            return '該聊天室無此邀請或該帳號已是成員';
        }
    }

    public function quitChat($chatMemberData)
    {
        $del_CM = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['account'])->first();
        if ($del_CM) {
            $del_CM->delete();
            $CMCount = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])->get();
            if (count($CMCount) < 1) {
                $del_Chat = ChatEloquent::find($chatMemberData['chat_id']);
                if ($del_Chat) {
                    $del_Chat->delete();
                    return '';
                } else {
                    return '001錯誤';
                }
            }
            return '';
        } else {
            return '您不屬於此聊天室';
        }
    }

    public function getCM($chatMemberData)
    {
        $CMCheck = ChatMemberEloquent::where('account', $chatMemberData['account'])
            ->where('chat_id', $chatMemberData['chat_id'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            $CMList = DB::table('chat_members')->where('chat_members.chat_id', $chatMemberData['chat_id'])
                ->where('chat_members.status', 2)
                ->select('chat_members.*', 'users.name', 'users.profile_pic')
                ->join('users', 'chat_members.account', '=', 'users.account')
                ->orderBy('chat_members.cm_id', 'desc')
                ->get();

            $baseService = new BaseService();
            $baseService->setAllTime($CMList);
            return $CMList;
        } else {
            return '您不是該群組成員';
        }

    }

    public function getUncheckCM($chatMemberData)
    {
        $CMCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['account'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            $CMList = DB::table('chat_members')->where('chat_id', $chatMemberData['chat_id'])
                ->where('status', 0)
                ->select('chat_members.account', 'chat_members.created_at', 'users.name', 'users.profile_pic')
                ->join('users', 'users.account', '=', 'chat_members.account')
                ->orderBy('chat_members.created_at', 'desc')
                ->get();
            $baseService = new BaseService();
            $baseService->setAllTime($CMList);
            return $CMList;
        } else {
            return '您不是該群組成員';
        }
    }

    public function getMyInvite($chatMemberData)
    {
        $CM_Chat = ChatMemberEloquent::join('chat', 'chatmember.chat_id', '=', 'chat.chat_id')
            ->where('chatmember.account', $chatMemberData['account'])
            ->where('chatmember.status', 1)
            ->select('chat.chat_id', 'chat.chat_name', 'chat.profile_pic')
            ->get();
        return $CM_Chat;
    }

    public function getMyChat($account)
    {
        $blackSQL = "messages.account not in (select blacked_account from blacks where black_account = '$account' union select black_account from blacks where blacked_account = '$account')";
        $dataList = DB::select("select chats.chat_id, chats.chat_name, chats.profile_pic as chat_profile_pic, messages.message_id, messages.account, messages.content, messages.type, messages.created_at, users.name, users.profile_pic as user_profile_pic, chat_members.status from chats " .
            "left join chat_members on chat_members.account = '$account' and chat_members.chat_id = chats.chat_id " .
            "left join messages on messages.message_id = (select message_id from messages where messages.chat_id = chats.chat_id and $blackSQL and messages.revoke = false order by created_at desc limit 1) and chat_members.status = 2 " .
            "left join users on messages.account = users.account " .
            "where chats.chat_id in (select chat_id from chat_members where account = '$account') " .
            "order by messages.created_at desc, chats.chat_id desc");
        $baseService = new BaseService();
        $baseService->setAllTime($dataList);
        return $dataList;
    }
}
