<?php

namespace App\Services;

use App\Report as ReportEloquent;
use App\Message as MessageEloquent;
use App\ChatMember as ChatMemberEloquent;

class ReportService
{
    public function createReport($reportData)
    {
        //檢舉訊息
        if ($reportData['type'] == 1) {
            $message = MessageEloquent::find($reportData['id']);
            if (!$message)
                return "無此訊息";

            $CMCheck = ChatMemberEloquent::where('account', $reportData['account'])
                ->where('chat_id', $message->chat_id)
                ->where('status', 2)->first();
            if (!$CMCheck)
                return "無權檢舉";
            ReportEloquent::create($reportData);
            return "";
        }
        return "error";
    }
}