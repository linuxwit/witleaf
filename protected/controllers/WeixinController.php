<?php

define("TOKEN", "weixin");

class WeixinController extends Controller {

    public function actionIndex() {
        if ($this->checkSignature()) {
            $this->responseMsg();
        }
    }

    public function valid() {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgType = $postObj->MsgType;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";


            //使用openId查询用户ID,
            //不存在,则注册,发出用户名，密码，网站地址
            //将图片插入到数据库存中
            //１　检查用户定义default目录，如果为空，创建default目录,
            //２　保存相片
            //成功返回相片地址
            //失败说明原因

            $userId = DataHelper::checkOpenId($fromUsername);
            if ($msgType == 'image') {
                $return = DataHelper::savePic($postObj->PicUrl, $userId);
                if ($return) {
                    $contentStr = "亲，收录成功了！";
                } else {
                    $contentStr = "亲，收录失败了！";
                }
                $msgType = "text";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                $msgType = "text";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '请发送图片');
                echo $resultStr;
            }
            exit;
        } else {
            echo "";
            exit;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}