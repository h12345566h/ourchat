<?php

namespace App\Services;

use App\User as UserEloquent;
use App\Blacks as BlacksEloquent;
use DB;

class BlackService
{
    public function createBlack($blackData)
    {
        if ($blackData['account'] != $blackData['blacked_account']) {
            $user1Check = UserEloquent::where('account', $blackData['account']);
            if ($user1Check) {
                $user2Check = UserEloquent::where('account', $blackData['blacked_account']);
                if ($user2Check) {
                    $BlackCheck = BlacksEloquent::where('black_account', $blackData['account'])
                        ->where('blacked_account', $blackData['blacked_account'])
                        ->first();
                    if ($BlackCheck) {
                        return '該成員已被封鎖，操作無效';
                    } else {
                        $blackData['black_account'] = $blackData['account'];
                        BlacksEloquent::create($blackData);
                        return '';
                    }
                } else {
                    return '該帳號不存在';
                }
            } else {
                return '你不是合法帳號';
            }
        } else {
            return '你無法將自己加入黑名單';
        }
    }

    public function deleteBlack($blackData)
    {
        $blackCheck = BlacksEloquent::where('black_account', $blackData['account'])
            ->where('blacked_account', $blackData['blacked_account'])
            ->first();
        if ($blackCheck) {
            $blackCheck->delete();
            return '';
        } else {
            return '003錯誤';
        }
    }

    public function getMyBlack($account)
    {
        $myBlackList = DB::table('blacks')->where('black_account', $account)
            ->leftjoin('users', 'blacks.blacked_account', '=', 'users.account')
            ->select('users.account', 'users.name', 'users.profile_pic')->get();
        return $myBlackList;
    }
}