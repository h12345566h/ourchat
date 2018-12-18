<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BlackService;
use Validator;


class BlackController extends Controller
{
    public $blackService;

    public function __construct()
    {
        $this->blackService = new BlackService();
    }


    //region createReport
    public function createBlack(Request $request)
    {
        $blackData = $request->all();
        $objValidator = Validator::make(
            $blackData,
            [
                'blacked_account' => 'required|string',
            ],
            [
                'blacked_account.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->blackService->createBlack($blackData);
        if ($result == "")
            return response()->json("已把該成員成功加入黑名單", 200, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region deleteBlack
    public function deleteBlack(Request $request)
    {
        $blackData = $request->all();
        $objValidator = Validator::make(
            $blackData,
            [
                'blacked_account' => 'required|string',
            ],
            [
                'blacked_account.*' => '002錯誤',
            ]
        );
        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $result = $this->blackService->deleteBlack($blackData);
        if ($result == "")
            return response()->json("已把該成員成功從黑名單移除", 200, [], JSON_UNESCAPED_UNICODE);
        else
            return response()->json([$result], 400, [], JSON_UNESCAPED_UNICODE);
    }
    //endregion

    //region 取得我的聊天室
    public function getMyBlack(Request $request)
    {
        $result = $this->blackService->getMyBlack($request->input("account"));
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

}
