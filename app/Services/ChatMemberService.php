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


class ChatMemberService
{


    public function addCM($chatMemberData)
    {
        $ChatCheck = ChatEloquent::find($chatMemberData['chat_id']);
        if ($ChatCheck) {
            $accCheck = UserEloquent::find($chatMemberData['account']);
            if ($accCheck) {
                $chatMemberData['status'] = 0;
                ChatMemberEloquent::create($chatMemberData);
                return '';
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
        $invCheck = ChatMemberEloquent::
        where('chat_id', $chatMemberData['chat_id'])->
        where('account', $chatMemberData['account'])->
        where('status', '=', 2)->first();
        if ($invCheck) {
            //被邀請人UserCheck
            $accCheck = UserEloquent::find($chatMemberData['inv_account']);
            if ($accCheck) {
                //被邀請人CMCheck
                $CMCheck = ChatMemberEloquent::
                where('chat_id', $chatMemberData['chat_id'])->
                where('account', $chatMemberData['inv_account'])->first();
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
//        return $CMCheck;
        if ($CMCheck) {
            if ($CMCheck->status == 0) {
                //准許者check
                $UserCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
                    ->where('account', $chatMemberData['account'])
                    ->where('status', 2)->first();
                if ($UserCheck) {
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
        $CMCheck = ChatMemberEloquent::
        where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['inv_account'])
            ->whereIn('status', [0, 1])->first();
        if ($CMCheck) {
            if ($CMCheck->status == 0) {
                //准許者check
                $UserCheck = ChatMemberEloquent::
                where('chat_id', $chatMemberData['chat_id'])
                    ->where('account', $chatMemberData['account'])
                    ->where('status', 2)->first();
                if ($UserCheck) {
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
            ->where('account', $chatMemberData['account'])
            ->where('status', 2)->first();
        if ($del_CM) {
            $del_CM->delete();
            return '';
        } else {
            return '您不屬於此聊天室';
        }
    }

    public function getCM($chatMemberData)
    {
        $CMList = ChatMemberEloquent:: where('chat_id', $chatMemberData['chat_id'])->get();
        return $CMList;
    }

    public function getUncheckCM($chatMemberData)
    {
        $CMCheck = ChatMemberEloquent::where('chat_id', $chatMemberData['chat_id'])
            ->where('account', $chatMemberData['account'])
            ->where('status', 2)->first();
        if ($CMCheck) {
            $CMList = ChatMemberEloquent:: where('chat_id', $chatMemberData['chat_id'])
                ->where('account', $chatMemberData['account'])
                ->where('status', 0)->get();
            return $CMList;
        } else {
            return '您不是該群組成員';
        }
    }

    public function getMyInvite($chatMemberData)
    {
        $CMList = ChatMemberEloquent::
        where('account', $chatMemberData['account'])
            ->where('status', 1)->get();
        return $CMList;
    }

    public function getMyChat($chatMemberData)
    {
        $CMList = ChatMemberEloquent::
        where('account', $chatMemberData['account'])
            ->where('status', 2)
            ->with(['chat' => function ($query) {
                $query->select(['chat_id', 'chat_name', 'creator', 'profile_pic']);
            }])->get();
        return $CMList;
    }
}