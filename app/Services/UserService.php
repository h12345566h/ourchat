<?php
/**
 * Created by PhpStorm.
 * User: tsaiminyuan
 * Date: 2018/1/2
 * Time: 下午4:03
 */

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

    public function register($postData)
    {
        $postData['password'] = bcrypt($postData['password']);
        UserEloquent::create($postData);
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
        $get_accountData = UserEloquent::where('account', 'like', "%$keyword%")
            ->orderByDesc('name')
            ->take(5)
            ->get();
        $get_nameData = UserEloquent::where('name', 'like', "%$keyword%")
            ->orderByDesc('name')
            ->take(5)
            ->get();
        $getUserData =$get_accountData->union($get_nameData);
        return $getUserData;
    }


}