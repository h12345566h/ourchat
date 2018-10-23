<?php

namespace App\Http\Controllers;

use App\Services\EchoTokenService;
use Illuminate\Http\Request;
use Validator;


class EchoController extends Controller
{
    public $echoTokenService;

    public function __construct()
    {
        $this->echoTokenService = new EchoTokenService();
    }

    #region 建立/刷新token
    public function createEchoToken(Request $request)
    {
        $postData = $request->all();
        $objValidator = Validator::make(
            $postData,
            [
                'type' => 'required|between:1,2',
                'old_token' => 'max:300',
                'new_token' => 'required|max:300'
            ],
            [
                'type.*' => 'type error',
                'old_token.*' => 'old_token error',
                'new_token.*' => 'new_token error'
            ]
        );

        if ($objValidator->fails())
            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);

        $this->echoTokenService->createEchoToken($postData);
        return response()->json('成功', 200, [], JSON_UNESCAPED_UNICODE);
    }

    #endregion


//    public function echo(Request $request)
//    {
//        $postData = $request->all();
//        $objValidator = Validator::make(
//            $postData,
//            [
//                'account' => 'required|max:20',
//                'content' => 'required|max:30'
//            ],
//            [
//                'account.*' => 'account error',
//                'content.*' => 'content error'
//            ]
//        );
//
//        if ($objValidator->fails())
//            return response()->json($objValidator->errors()->all(), 400, [], JSON_UNESCAPED_UNICODE);
//
//        $echoTokenArr = $this->echoTokenService->getUserToken($postData['account']);
//        $firebaseTokenList = "";
//        $apnsTokenList = "";
//        foreach ($echoTokenArr as $echoToken) {
//            if ($echoToken['type'] == 1)
//                $firebaseTokenList .= $echoToken['token'] . ",";
//            else
//                $apnsTokenList .= $echoToken['token'] . ",";
//        }
//        if (strlen($firebaseTokenList) > 0)
//            $firebaseTokenList = substr($firebaseTokenList, 0, strlen($firebaseTokenList) - 1);
//
//        if (strlen($apnsTokenList) > 0)
//            $apnsTokenList = substr($apnsTokenList, 0, strlen($apnsTokenList) - 1);
//
//        $client = new Client();
//        try {
//            $url = "http://localhost:8080/EchoServlet?to_account=" . $postData['account'] .
//                "&firebase_token_str=$firebaseTokenList" . "&apns_token_str=$apnsTokenList" . "&content=" . $postData['content'];
//            $res = $client->request('GET', $url);
//        } catch (GuzzleException $e) {
//            return response()->json($e->getMessage(), 400);
//        }
//        $resData = json_decode((string)$res->getBody());
//        return response()->json($resData, $res->getStatusCode(), [], JSON_UNESCAPED_UNICODE);
//    }


}
