<?php

// +----------------------------------------------------------------------
// | 微信公众平台素材管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.5mell.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Loch Kan <454831746@qq.com>
// +----------------------------------------------------------------------








class UserAction extends AdminbaseAction {
    
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
        $openid = I('get.openid');

        $userListDatas = $this->Wechat->getUserList($openid);
        
        
        $datas = array();
        foreach ($userListDatas['data']['openid'] as $key => $value) {
            $datas[$key] = $this->Wechat->getUserInfo($value);
            $datas[$key]['subscribe_time'] = date('y-m-d H:i:s',$datas[$key]['subscribe_time']);

        }
        
        if(IS_AJAX){
            $callback['status'] = 1 ;            
            $userListDatas['list'] = $datas;
            $callback['datas'] = $userListDatas;
            echo json_encode($callback);
            exit();
        }else{
            $this->assign("userListDatas", $userListDatas);        
            $this->assign("datas",$datas );
            $this->display();
        }
        
    }




}
