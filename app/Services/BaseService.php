<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2018/10/3
 * Time: 15:31
 */

namespace App\Services;

use Carbon\Carbon;

class BaseService
{
    public function timeDistance($time)
    {
        $delimiter = '-';
        $now = Carbon::now('Asia/Taipei');

        $Distance = strtotime($now) - strtotime($time);

        if ($Distance < 60) {
            return $Distance + '秒前';
        } else if ($Distance < 3600) {
            return floor($Distance / 60) . '分鐘前';
        } else if ($Distance < 86400) {
            return floor($Distance/3600) . '小時前';
        } else if ($Distance < 5184000) {
            return floor($Distance / 86400) . '天前';
        } else {
            $startTimeArr = explode($delimiter, $time->toDateTimeString());
            $endTimeArr = explode($delimiter, now());
            if ($endTimeArr[0] - $startTimeArr[0] > 0) {
                return $endTimeArr[0] - $startTimeArr[0] . '年前';
            } else {
                return $endTimeArr[1] - $startTimeArr[1] . '個月前';
            }
        }
    }
}