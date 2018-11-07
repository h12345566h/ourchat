<?php

namespace App\Services;

use Carbon\Carbon;

class BaseService
{
    function setAllTime(&$dataList)
    {
        foreach ($dataList as $item) {
            if ($item->created_at) {
                $time = $this->setTime($item->created_at);
                $item->created_at = $time;
            }
        }
    }

    public function setTime($time)
    {
        $now = Carbon::now();
        $carbonTime = Carbon::parse($time);
//        $distance = strtotime($now) - strtotime($time);

        if ($carbonTime->hour < 12) {
            if ($carbonTime->hour == 0)
                $resStr = '上午 12:' . str_pad($carbonTime->minute, 2, "0", STR_PAD_LEFT);
            else
                $resStr = '上午 ' . $carbonTime->hour . ':' . str_pad($carbonTime->minute, 2, "0", STR_PAD_LEFT);
        } else {
            if ($carbonTime->hour == 12)
                $resStr = '下午 12:' . str_pad($carbonTime->minute, 2, "0", STR_PAD_LEFT);
            else
                $resStr = '下午 ' . ($carbonTime->hour - 12) . ':' . str_pad($carbonTime->minute, 2, "0", STR_PAD_LEFT);
        }
        if ($carbonTime->dayOfYear != $now->dayOfYear)
            $resStr = $carbonTime->month . '/' . $carbonTime->day . ' ' . $resStr;
        if ($carbonTime->year != $now->year)
            $resStr = $carbonTime->year . '/' . $resStr;

        return $resStr;
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