<?php

namespace App\Services;

use App\User as UserEloquent;
use Hash;
use Image;
use Storage;

class UserService
{
    public function login($postData)
    {
        $user = UserEloquent::find($postData['account']);
        if ($user) {
            if (Hash::check($postData['password'], $user->password)) {
                return '';
            }
            return '密碼錯誤';
        }
        return '無此帳號請去註冊';
    }

    public function passwordChange($postData)
    {
        $user = UserEloquent::find($postData['account']);
        if ($user) {
            if (Hash::check($postData['old_password'], $user->password)) {
                if ($postData['new_password'] == $postData['new_password2']) {
                    $user['password'] = bcrypt($postData['new_password']);
                    $user->save();
                    return '';
                } else {
                    return '新密碼與新密碼確認不相符';
                }
            } else {
                return '輸入了錯誤的舊密碼';
            }
        } else {
            return '無此帳號請去註冊';
        }
    }

    public function register($postData)
    {
        $postData['password'] = bcrypt($postData['password']);
        UserEloquent::create($postData);
    }

    public function editUser($postData)
    {
        $user = UserEloquent::find($postData['account']);
        if ($user) {
            $user->name = $postData['name'];
            $user->email = $postData['email'];
            $user->save();
            return '';
        } else {
            return '無此帳號';
        }
    }

    public function updateProfilePic(\Illuminate\Http\UploadedFile $file, $account)
    {
        $newFileName = date("YmdHis", time()) . '___' . rand(1000, 9999) . '___' . $file->getClientOriginalName();
        if (strlen($newFileName) > 200)
            return '0';
        $image = Image::make($file);
        $image->resize(350, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save('images/profile_pic/' . $newFileName);
        $user = UserEloquent::find($account);
        if ($user->profile_pic) {
            if (file_exists('images/profile_pic/' . $user->profile_pic)) {
                unlink('images/profile_pic/' . $user->profile_pic);
            }
        }
        $user->profile_pic = $newFileName;
        $user->save();
        return $newFileName;
    }

    public function searchUser($userData)
    {
        $keyword = $userData['keyword'];
        $getUserData = UserEloquent::where('account', 'like', "%$keyword%")
            ->orwhere('name', 'like', "%$keyword%")
            ->orderByDesc('name')
            ->take(10)
            ->get();
        return $getUserData;
    }
}