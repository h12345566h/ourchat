<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    //region createReport
    public function createReport(Request $request)
    {
        $reportData = $request->all();
        $objValidator = Validator::make(
            $reportData,
            [
                'id' => 'required|integer',
                'type' => 'required|integer',
            ],
            [
                'id.*' => '001錯誤',
                'type.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->reportService->createReport($reportData);
        if ($result == "")
            return response()->json("檢舉成功", 200, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion
}
