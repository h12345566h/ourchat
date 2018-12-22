<?php

namespace App\Services;

use Carbon\Carbon;

class BaseService
{
    function setAllTime($dataList)
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

    public function setRead($dataList, $readList)
    {
        foreach ($dataList as $item_A) {
            $item_A->readlist = array();
            foreach ($readList as $item_B) {
                if ($item_B->message_id == $item_A->message_id) {
                    array_push($item_A->readlist, $item_B);
                }
            }
        }
    }

}