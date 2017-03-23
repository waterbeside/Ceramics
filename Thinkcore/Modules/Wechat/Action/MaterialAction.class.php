<?php

// +----------------------------------------------------------------------
// | 微信公众平台素材管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------



// class MaterialAction extends AdminbaseAction {
    
//     protected $wid;
//     function _initialize() {
//         parent::_initialize();
//         if(!IS_POST){
//             $wid = I('get.wid',"","strtoupper");
//             if(!$wid){
//                 $wid = cookie('Wechat_wid_current');
//                 if($wid){
//                     $_GET['wid'] = $wid;
//                     $this->redirect('', $_GET);
//                 }
//             }else{
//                 cookie('Wechat_wid_current',$wid);
//             }
//             $this->wid = $wid ; 
//         }
//     }

//     //站点列表
//     public function index() {
//         $wid = $this->wid;
//         $DB_Wechat = D('Wechat/Wechat');
        
//         $wechatList  = $DB_Wechat->getWechatWids();
//         $wechatInfo = $wechatList[$wid]; 
//         $wechat_token = $wechatInfo['token'];
//         $encodingaeskey = $wechatInfo['encodingaeskey'];
//         $appid = $wechatInfo['appid'];
//         $appsecret = $wechatInfo['appsecret'];
        
//         import('Util.TPWechat', LIB_PATH);
//         //import('Util.Wechat', LIB_PATH);
//         $options = array(
//            'token'=>$wechat_token, //填写你设定的key
//            'encodingaeskey'=> $encodingaeskey, //填写加密用的EncodingAESKey
//            'appid'=>$appid, //填写高级调用功能的app id, 请在微信开发模式后台查询
//            'appsecret'=>$appsecret //填写高级调用功能的密钥
//         );
//         $weObj = new TPWechat($options);

//         $list = $weObj->getUserList();
//         var_dump($weObj);
//         var_dump($list);
//         //$this->display();
//     }




//}






class MaterialAction extends AdminbaseAction {
    
    protected $wid;
    protected $db_wechat;
    protected $wechatList;
    protected $wechatInfo;
    protected $Wechat;

    function _initialize() {
        parent::_initialize();
        $wid = I('request.wid',"","strtoupper");
        if(!IS_POST){            
            if(!$wid){
                $wid = cookie('Wechat_wid_current');
                if($wid){
                    $_GET['wid'] = $wid;
                    $this->redirect('', $_GET);
                }
            }else{
                cookie('Wechat_wid_current',$wid);
            }            
        }
        $this->wid = $wid ; 
        $this->db_wechat = D('Wechat/Wechat');
        
        $this->wechatList  = $this->db_wechat->getWechatWids();
        $this->wechatInfo = $this->wechatList[$wid]; 

        
        import('Util.TPWechat', LIB_PATH);
        //import('Util.Wechat', LIB_PATH);
        $options = array(
           'token'=>$this->wechatInfo['token'], //填写你设定的key
           'encodingaeskey'=> $this->wechatInfo['encodingaeskey'], //填写加密用的EncodingAESKey
           'appid'=>$this->wechatInfo['appid'], //填写高级调用功能的app id, 请在微信开发模式后台查询
           'appsecret'=>$this->wechatInfo['appsecret'] //填写高级调用功能的密钥
        );

        $this->Wechat = new TPWechat($options);
        $this->assign("wid", $this->wid);
        $this->assign("wechatList", $this->wechatList);
        $this->assign("wechatInfo", $this->wechatInfo);
        

    }

    //站点列表
    public function index() {
        $type = I('get.type','','strtolower');
        $total_array = $this->Wechat->getForeverCount();
        $total = $total_array[$type.'_count'];
        $pagesize = 20 ;
        $Page = $this->page($total, $pagesize);

        $datas = $this->Wechat->getForeverList($type,$Page->firstRow,$pagesize);
        
        $this->assign("total_array", $total_array);
        $this->assign("type", $type);
        $this->assign("Page", $Page->show()); 
        $this->assign("datas", $datas);
        $this->display();
    }




}
