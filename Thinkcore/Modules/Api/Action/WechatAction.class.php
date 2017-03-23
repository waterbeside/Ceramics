<?php
/**
 * 微信公众号接口url
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class WechatAction extends Action {
    public function index() {

        $wid = I('get.wid',"","strtoupper");
        $DB_Wechat = D('Wechat/Wechat');
        $wechatList  = $DB_Wechat->getWechatWids();

        $wechatInfo = $wechatList[$wid]; 
        $wechat_token = $wechatInfo['token'];
        $appid = $wechatInfo['appid'];
        $appsecret = $wechatInfo['appsecret']; 
        
        import('Util.TPWechat', LIB_PATH);
        //import('Util.Wechat', LIB_PATH);

        $options = array(
           'token'=>$wechat_token, //填写你设定的key
           'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
           'appid'=>$appid, //填写高级调用功能的app id, 请在微信开发模式后台查询
           'appsecret'=>$appsecret //填写高级调用功能的密钥
        );
        $weObj = new TPWechat($options);         
        $weObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $Rev = $weObj->getRev();
        $type = $Rev->getRevType();

        switch($type) {
            case TPWechat::MSGTYPE_TEXT:
                    //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
                    $replyType = 'txt';
                    $postStr = $Rev->getRevContent();
                    switch (strtolower($postStr)) {
                        case 'time':
                            $msg = date("Y-m-d H:i:s");
                            break; 
                        default:
                            if(strpos($postStr, "搜:")===0||strpos($postStr, "搜：")===0){
                                $strLen = mb_strlen($postStr,'utf-8');
                                $keywork = mb_substr($postStr,2,$strLen-2,'utf-8');

                                $msg    = "查产品【".$keywork."】得0条结果。";
                                $msg    .= "<a href='#'>点击查看</a>";
                                $callbackData = array(
                                    "0"=>array(
                                        'Title'=>'搜索结果',
                                        'Description'=>'summary text',
                                        'PicUrl'=>'http://www.domain.com/1.jpg',
                                        'Url'=>'http://www.domain.com/1.html'
                                    ), 
                                    
                                );
                               
                                $replyType = 'news';
                                break;
                            }

                            $msg = "欢迎".$Rev->getRevFrom();
                            break;
                    }
                    if($replyType=='txt'){
                        $weObj->text($msg)->reply();    
                    }
                    if($replyType=='news'){
                        $weObj->news($callbackData)->reply();
                    }
                    exit;
                    break;
            case TPWechat::MSGTYPE_EVENT:
                    $event = $weObj->getRevEvent();
                    switch ($event['event']) {
                        case 'subscribe':
                            $weObj->text('欢迎关注'.$wechatInfo['name'])->reply(); 
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                   
                    break;
            case TPWechat::MSGTYPE_IMAGE:
                    break;
            default:
                    $weObj->text("help info")->reply();
        }
    }

}