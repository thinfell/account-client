<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use panwenbin\helper\Curl;

class SsoController extends Controller
{
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin($AuthenTickitRequestParamName)
    {
        $timestamp = microtime(true) * 10000;
        $sign = md5(Yii::$app->params['sso_website_id'].Yii::$app->params['sso_website_secret'].$timestamp);
        $result = Curl::to(Yii::$app->params['sso_website_server'].'/login')->withData(['sso_website_id' => Yii::$app->params['sso_website_id'], 'sign' => $sign, 'timestamp' => $timestamp, 'AuthenTickitRequestParamName' => $AuthenTickitRequestParamName])->get();
        $result = json_decode($result);
        if($result->code == 200){
            $userid = $result->message;
            //返回值 [$userid]
             return Yii::$app->user->login(User::findByUserid($userid), 0);
        }else{
            Yii::$app->response->data =['code' => 0, 'message' => $result->message];
            return false;
        }
    }

    public function actionRegister($AuthenTickitRequestParamName)
    {
        $timestamp = microtime(true) * 10000;
        $sign = md5(Yii::$app->params['sso_website_id'].Yii::$app->params['sso_website_secret'].$timestamp);
        $result = Curl::to(Yii::$app->params['sso_website_server'].'/register')->withData(['sso_website_id' => Yii::$app->params['sso_website_id'], 'sign' => $sign, 'timestamp' => $timestamp, 'AuthenTickitRequestParamName' => $AuthenTickitRequestParamName])->get();
        $result = json_decode($result);
        if($result->code == 200){
            $user = $result->message;
            //返回值 [$user->userid]  [$user->name]  [$user->mobile]
            //注册$user
            //登录$user
            return $user->userid;
        }else{
            Yii::$app->response->data =['code' => 0, 'message' => $result->message];
            return false;
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        return Yii::$app->user->logout();
    }
}
