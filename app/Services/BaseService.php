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
        $now = Carbon::now();
        $carbonTime = Carbon::parse($time);
        $Distance = strtotime($now) - strtotime($time);

        if ($Distance < 86400) {
            //一天內
            if ($carbonTime->hour < 12) {
                return '上午 ' . $carbonTime->hour . ':' . $carbonTime->minute;
            } else {
                return '下午' . ($carbonTime->hour - 12) . ':' . $carbonTime->minute;
            }
        } else {
            //一天前

            //一年前
            if ($carbonTime->year < $now->year) {
                if ($carbonTime->hour < 12) {
                    return $carbonTime->year . '/' . $carbonTime->month . '/' . $carbonTime->day . ' 上午 ' . $carbonTime->hour . ':' . $carbonTime->minute;
                } else {
                    return $carbonTime->year . '/' . $carbonTime->month . '/' . $carbonTime->day . ' 下午 ' . ($carbonTime->hour - 12) . ':' . $carbonTime->minute;
                }
            } else {
                //一年內
                if ($carbonTime->hour < 12) {
                    return $carbonTime->month . '/' . $carbonTime->day . ' 上午 ' . $carbonTime->hour . ':' . $carbonTime->minute;
                } else {
                    return $carbonTime->month . '/' . $carbonTime->day . ' 下午 ' . ($carbonTime->hour - 12) . ':' . $carbonTime->minute;
                }
            }
        }
    }


//    public function json2String($jsonData)
//    {
//        return json_encode($jsonData, JSON_FORCE_OBJECT);
//    }


//        if ($Distance < 60) {
//            return $Distance . '秒前';
//        } else if ($Distance < 3600) {
//            return floor($Distance / 60) . '分鐘前';
//        } else if ($Distance < 86400) {
//            return floor($Distance / 3600) . '小時前';
//        } else if ($Distance < 5184000) {
//            return floor($Distance / 86400) . '天前';
//        } else {
//            $startTimeArr = explode($delimiter, $time->toDateTimeString());
//            $endTimeArr = explode($delimiter, now());
//            if ($endTimeArr[0] - $startTimeArr[0] > 0) {
//                return $endTimeArr[0] - $startTimeArr[0] . '年前';
//            } else {
//                return $endTimeArr[1] - $startTimeArr[1] . '個月前';
//            }
//    }
}