<?php
/**
 * Created by PhpStorm.
 * User: tsaiminyuan
 * Date: 2018/10/6
 * Time: 下午4:57
 */

namespace App\Services;

use App\EchoToken as EchoTokenEloquent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EchoTokenService
{


    public function getUserToken($account)
    {
        $echoTokenArr = EchoTokenEloquent::where('account', $account)->get();
        return $echoTokenArr;
    }

    public function createEchoToken($postData)
    {
        if (array_key_exists('old_token', $postData)) {
            $oldToken = EchoTokenEloquent::where('token', $postData['old_token'])
                ->where('type', $postData['type'])
                ->first();
            if ($oldToken) {
                if ($oldToken->account != $postData['account'])
                    $oldToken->account = $postData['account'];
                $oldToken->token = $postData['new_token'];
                $oldToken->save();
                return;
            }
        } else {
            $token = EchoTokenEloquent::where('token', $postData['new_token'])
                ->where('type', $postData['type'])
                ->first();
            if ($token) {
                if ($token->account != $postData['account']) {
                    $token->account = $postData['account'];
                    $token->save();
                }

                return;
            }
        }
        $postData['token'] = $postData['new_token'];
        EchoTokenEloquent::create($postData);
    }

    #endregion

    public function echo($postData)
    {
        $echoTokenArr = $this->echoTokenService->getUserToken($postData['account']);
        $firebaseTokenList = "";
        $apnsTokenList = "";
        foreach ($echoTokenArr as $echoToken) {
            if ($echoToken['type'] == 1)
                $firebaseTokenList .= $echoToken['token'] . ",";
            else
                $apnsTokenList .= $echoToken['token'] . ",";
        }
        if (strlen($firebaseTokenList) > 0)
            $firebaseTokenList = substr($firebaseTokenList, 0, strlen($firebaseTokenList) - 1);

        if (strlen($apnsTokenList) > 0)
            $apnsTokenList = substr($apnsTokenList, 0, strlen($apnsTokenList) - 1);

        $client = new Client();
        try {
            $url = "http://localhost:8080/EchoServlet?to_account=" . $postData['account'] .
                "&firebase_token_str=$firebaseTokenList" . "&apns_token_str=$apnsTokenList" . "&content=" . $postData['content'];
            $res = $client->request('GET', $url);
        } catch (GuzzleException $e) {
            return response()->json($e->getMessage(), 400);
        }
        $resData = json_decode((string)$res->getBody());
        return $resData;
    }
}